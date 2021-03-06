<?php

namespace Inspirium\BookProposition\Observers;

use Inspirium\BookProposition\Models\ApprovalRequest;
use Inspirium\BookProposition\Notifications\ApprovalRequestAccepted;
use Inspirium\BookProposition\Notifications\ApprovalRequestDenied;
use Inspirium\TaskManagement\Models\Task;

class ApprovalRequestObserver {

	public function assigned(ApprovalRequest $request) {
		$task = new Task();
		$task->description = $request->description;
		$task->assigner()->associate($request->requester);
		$task->assignee()->associate($request->requestee);
		$task->related()->associate($request);
		$task->name = $request->name;
		$task->status = 'new';
		$task->type = 3;
		$task->related_link = '/proposition/'.$request->proposition_id.'/expenses/compare';
		$task->save();
		$task->assignNewThread();
	}

	public function accepted(ApprovalRequest $request) {
		$request->requester->notify(new ApprovalRequestAccepted($request));
	}

	public function denied(ApprovalRequest $request) {
		$request->requester->notify(new ApprovalRequestDenied($request));
	}
}