<?php

namespace Inspirium\BookProposition\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Inspirium\BookProposition\Models\ApprovalRequest;
use Inspirium\TaskManagement\Models\Task;

class PropositionDenied extends Notification
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
        $notifications = $notifiable->notification_settings;
        $out = ['database', 'broadcast'];
        if ( $notifications === 1 || (isset($notifications['proposition_denied']) && $notifications['proposition_denied'])) {
            $out[] = 'mail';
        }
        return $out;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $values = $this->toArray($notifiable);
        return (new MailMessage)
            ->line($values['title'])
            ->line($values['message'])
            ->action('View', url($values['link']));
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
		    'title' => __('Proposition has been denied'),
		    'message' => __(':requestee has denied your request: :related', ['requestee' => $this->task->assignee->name, 'related' => $this->task->related->project_name]),
		    'link' => '/proposition/'.$this->task->related->id. '/edit/start',
		    'tasktype' => $this->task->formatted_type,
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
