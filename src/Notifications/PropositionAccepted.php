<?php

namespace Inspirium\BookProposition\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Inspirium\BookProposition\Models\ApprovalRequest;
use Inspirium\TaskManagement\Models\Task;

class PropositionAccepted extends Notification
{
    use Queueable;

    private $task = false;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
	    return [
		    'title' => __('Proposition has been accepted'),
		    'message' => __(':requestee has accepted your request: :related', ['requestee' => $this->task->assignee->name, 'related' => $this->task->related->project_name]),
		    'link' => '/proposition/'.$this->task->related->id. '/edit/start',
		    'tasktype' => [ 'title' => __('Approval Request'), 'className' => 'tasktype-3'],
		    'sender' => [
			    'name' => $this->task->assignee->name,
			    'image' => $this->task->assignee->image,
			    'link' => $this->task->assignee->link
		    ]
	    ];
    }

	public function toBroadcast($notifiable)
	{
		return new BroadcastMessage([ 'data' => $this->toArray($notifiable)]);
	}
}
