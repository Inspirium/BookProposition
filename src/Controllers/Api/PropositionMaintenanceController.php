<?php
namespace Inspirium\BookProposition\Controllers\Api;

use Inspirium\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inspirium\BookProposition\Models\ApprovalRequest;
use Inspirium\BookProposition\Models\BookProposition;
use Inspirium\Models\HumanResources\Employee;
use Inspirium\TaskManagement\Models\Task;

class PropositionMaintenanceController extends Controller {

	public function deleteProposition( $id ) {
		BookProposition::destroy( $id );

		return response()->json( [] );
	}

	public function forceDeleteProposition($id) {
		$proposition = BookProposition::withTrashed()->find($id);
		try {
			$this->authorize( 'forceDelete', $proposition );
		}
		catch (AuthorizationException $e) {
			return response()->json(['error' => 'unauthorized'], 403);
		}
		$proposition->forceDelete();
		return response()->json( [] );
	}

	public function restoreProposition( $id ) {
		$proposition = BookProposition::withTrashed()->find( $id );
		$proposition->restore();

		return response()->json( [] );
	}

	public function assignProposition( Request $request, $id ) {
		$proposition = BookProposition::find( $id );
		$employees   = $request->input( 'employees' );
		$assigner    = \Auth::user();
		if ( $employees ) {
			$task      = new Task();
			$task->assigner()->associate( $assigner );
			$task->assignee_id = $employees[0]['id'];
			$assignee = Employee::find($employees[0]['id']);
			$task->name = __('Proposition') . ': ' . $proposition->title;
			$task->related()->associate( $proposition );
			$task->description = $request->input('description')?$request->input('description'):'';
			if ($request->input('access') === 'onepage') {
				$task->related_link = $request->input('path');
				$step = $request->input('step');
				$proposition->editors()->attach($employees[0]['id'], ['step' => $step, 'complete' => false]);
			}
			else {
				$proposition->editors()->attach($employees[0]['id'], ['complete' => true, 'step' => 0]);
			}
			$task->status      = 'new';
			$task->priority = $request->input('priority')?$request->input('priority'):'low';
			$task->order = $assignee->tasks->count() + 1;
			$task->new_order = $assignee->tasks->count() + 1;
			if ($request->input('date')) {
				$task->deadline = Carbon::createFromFormat( '!d. m. Y.', $request->input( 'date' ) );
			}
			else {
				$task->deadline = null;
			}
			$task->type     = 1;
			$task->save();
			$task->assignNewThread();

		}
	}

	public function assignDocument(Request $request, $id) {
		$proposition = BookProposition::find( $id );
		$employees   = $request->input( 'employees' );
		$assigner    = \Auth::user();
		$task      = new Task();
		$task->assigner()->associate( $assigner );
		$task->assignee_id = $employees[0]['id'];
		$task->name = __('Proposition') . ': ' . $proposition->title;
		$task->related()->associate( $proposition );
		$task->description = $request->input('description')?$request->input('description'):'';
		$type = $request->input('dir');
		$task->related_link = $request->input('path');
		$task->status      = 'new';
		$task->priority = $request->input('priority')?$request->input('priority'):'low';
		if ($request->input('date')) {
			$task->deadline = Carbon::createFromFormat( '!d. m. Y.', $request->input( 'date' ) );
		}
		else {
			$task->deadline = null;
		}
		$task->type     = 4;
		$assignee = Employee::find($employees[0]['id']);
		$task->order = $assignee->tasks->count() + 1;
		$task->new_order = $assignee->tasks->count() + 1;
		$step = $request->input('step');
		$proposition->editors()->attach($employees[0]['id'], ['step' => $step, 'complete' => 0]);

		$task->save();
		$files = [
			'files' => $proposition->documents()->wherePivot( 'type', $type )->wherePivot( 'final', false )->get(),
			'final' => $proposition->documents()->wherePivot( 'type', $type )->wherePivot( 'final', true )->get()
		];
		foreach ($files['files'] as $file) {
			$task->documents()->attach($file->id, ['is_final' => false ]);
		}

		foreach ($files['final'] as $file) {
			$task->documents()->attach($file->id, ['is_final' => true ]);
		}
		$task->assignNewThread();
	}

	public function requestApproval( Request $request, $id) {
		$approval_request = new ApprovalRequest([
			'budget' => $request->input('budget'),
			'expense' => $request->input('expense'),
			'name' => $request->input('name'),
			'description' => $request->input('description')?$request->input('description'):' ',
			'designation' => $request->input('designation'),
			'status' => 'requested',
			'proposition_id' => $id,
			'requester_id' => \Auth::id(),
			'requestee_id' => $request->input('requestees')[0]['id']
		]);
		$approval_request->save();

		$approval_request->triggerAssigned();
		return response()->json([]);
	}

	public function approvalProposition(Request $request, $id) {
		$proposition = BookProposition::find($id);
		$proposition->status= 'requested';
		$proposition->approved_on = Carbon::now();
		$proposition->save();
		$task = new Task();
		$task->type = 5;
		$task->name = $proposition->title;
		$assigner = \Auth::user();
		$task->assigner()->associate($assigner);
		$task->description = $request->input('description');
		$task->status = 'new';
		$task->assignee_id = $request->input('employees')[0]['id'];
		$assignee = Employee::find($request->input('employees')[0]['id']);
		$task->order = $assignee->tasks->count() + 1;
		$task->new_order = $assignee->tasks->count() + 1;
		$task->related()->associate($proposition);
		$task->save();
		$task->assignNewThread();
	}
}