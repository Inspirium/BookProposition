<?php
namespace Inspirium\BookProposition\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inspirium\BookProposition\Models\ApprovalRequest;
use Inspirium\BookProposition\Models\BookProposition;
use Inspirium\HumanResources\Models\Employee;

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
		$assigner    = Employee::where( 'user_id', Auth::id() )->first();
		/*if ( $employees ) {
			$employees = array_pluck( $employees, 'id' );
			$task      = new Task();
			$task->assigner()->associate( $assigner );
			$task->name = 'Proposition: ' . $proposition->title;
			$task->related()->associate( $proposition );
			$task->description = $request->input('description');
			if ($request->input('access') === 'onepage') {
				$task->description .= ' <a href="'.$request->input('path').'">Link</a>';
			}
			$task->status      = 'new';
			$task->priority = $request->input('priority');
			$task->deadline = $request->input('date');
			$task->type        = 1;
			$task->save();
			$task->employees()->attach( $employees );
			foreach ( $employees as $employee_id ) {
				$employee = Employee::find( $employee_id );
				$employee->user->notify( new TaskAssigned( $task ) );
			}
		} */
	}

	public function requestApproval( Request $request, $id) {
		$approval_request = new ApprovalRequest([
			'budget' => $request->input('budget'),
			'expense' => $request->input('expense'),
			'name' => $request->input('name'),
			'description' => $request->input('description'),
			'status' => 'requested',
			'proposition_id' => $id,
			'requester_id' => \Auth::id()
		]);
		$approval_request->save();

		$requestees = collect($request->input('requestees'))->map(function($e) {
			return $e['id'];
		});
		$approval_request->requestees()->sync($requestees);

		$approval_request->triggerAssigned();
		return response()->json([]);
	}
}