<?php
namespace Inspirium\BookProposition\Controllers\Api;

use Inspirium\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inspirium\BookProposition\Models\ApprovalRequest;
use Inspirium\BookProposition\Models\BookProposition;
use Inspirium\TaskManagement\Models\Task;

class PropositionMaintenanceController extends Controller {

	public function deleteProposition( $id ) {
		BookProposition::destroy( $id );

		return response()->json( [] );
	}

	public function restoreProposition( $id ) {
		$proposition = BookProposition::withTrashed()->find( $id );
		$proposition->restore();

		return response()->json( [] );
	}

	public function assignProposition( Request $request, $id ) {
		$proposition = BookProposition::find( $id );
		$departments = $request->input( 'departments' );
		$employees   = $request->input( 'employees' );
		$assigner    = \Auth::user();
		if ( $employees ) {
			$task      = new Task();
			$task->assigner()->associate( $assigner );
			$task->assignee_id = $employees[0]['id'];
			$task->name = __('Proposition') . ': ' . $proposition->title;
			$task->related()->associate( $proposition );
			$task->description = $request->input('description');
			if ($request->input('access') === 'onepage') {
				$task->related_link = $request->input('path');
			}
			$task->status      = 'new';
			$task->priority = $request->input('priority');
			$task->deadline = Carbon::createFromFormat('!d. m. Y.', $request->input('date'));
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
		$task->description = $request->input('description');
		$type = $request->input('dir');
		$task->related_link = $request->input('path');
		$task->status      = 'new';
		$task->priority = $request->input('priority');
		$task->deadline = Carbon::createFromFormat('!d. m. Y.', $request->input('date'));
		$task->type     = 1;
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
		$task->name = __('Proposition Approval');
		$assigner = \Auth::user();
		$task->assigner()->associate($assigner);
		$task->description = $request->input('description');
		$task->status = 'new';
		$task->assignee_id = $request->input('employees')[0]['id'];
		$task->related()->associate($proposition);
		$task->save();
		$task->assignNewThread();
	}
}