<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RideNotification extends Notification
{
    use Queueable;

    protected $rideDetails;

    public function __construct($rideDetails)
    {
        $this->rideDetails = $rideDetails;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // return ['mail'];
        return ['database', 'mail']; // or 'sms', 'push', etc.
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // return (new MailMessage)
        //     ->line('The introduction to the notification.')
        //     ->action('Notification Action', url('/'))
        //     ->line('Thank you for using our application!');

        return (new MailMessage)
            ->subject('Upcoming Ride Notification')
            ->line('Your ride is scheduled to start in 1 hour!')
            ->action('View Ride Details', url('/rides/' . $this->rideDetails->id))
            ->line('Thank you for using our service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ride_id' => $this->rideDetails->id,
            'message' => 'Your ride is scheduled to start in 1 hour!',
        ];
    }
}
