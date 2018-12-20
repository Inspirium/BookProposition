<?php

namespace Inspirium\BookProposition\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Inspirium\BookProposition\Models\ApprovalRequest;

class ApprovalRequestAccepted extends Notification
{
    use Queueable;

    private $request = false;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ApprovalRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $notifications = $notifiable->notifications;
        $out = ['database', 'broadcast'];
        if ( $notifications === 1 || (isset($notifications['task_assigned']) && $notifications['task_assigned'])) {
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
		    'title' => __('Approval Request has been accepted'),
		    'message' => __(':requestee has accepted your request in :related', ['requestee' => $this->request->requestee->name, 'related' => $this->request->proposition->project_name]),
		    'link' => '/proposition/'.$this->request->proposition->id. '/expenses/compare',
		    'tasktype' => $this->request->tasks[0]->formatted_type,
		    'sender' => [
			    'name' => $this->request->requestee->name,
			    'image' => $this->request->requestee->image,
			    'link' => $this->request->requestee->link
		    ]
	    ];
    }

	public function toBroadcast($notifiable)
	{
		return new BroadcastMessage([ 'data' => $this->toArray($notifiable)]);
	}
}
