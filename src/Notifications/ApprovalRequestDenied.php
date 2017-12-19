<?php

namespace Inspirium\BookProposition\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Inspirium\BookProposition\Models\ApprovalRequest;

class ApprovalRequestDenied extends Notification
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
		    'title' => __('Approval Request has been denied'),
		    'message' => __(':requestee has denied your request in :related', ['requestee' => $this->request->requestee->name, 'related' => $this->request->proposition->project_name]),
		    'link' => '/proposition/'.$this->request->proposition->id. '/expenses/compare',
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
