<?php

namespace Inspirium\BookProposition\Observers;

use Inspirium\BookProposition\Models\ApprovalRequest;
use Inspirium\TaskManagement\Models\Task;

class ApprovalRequestObserver {

	public function assigned(ApprovalRequest $request) {
		$task = new Task();
		$task->description = $request->description;
		$task->assigner()->associate($request->requester);
		$task->name = $request->name;
		$task->status = 'new';
		$task->type = 3;
		$task->save();
		$task->employees()->sync($request->requestees);
		$task->triggerAssigned();
	}

	public function deleted(ApprovalRequest $request) {
		//todo: send notification
	}

	public function updated(ApprovalRequest $request) {
		dd($request);
	}
}