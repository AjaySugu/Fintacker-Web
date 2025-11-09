<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WebPush\WebPushMessage;

class SubscriptionReminder extends Notification
{
    use Queueable;

    protected $messageText;

    public function __construct($messageText)
    {
        $this->messageText = $messageText;
    }

    public function via($notifiable)
    {
        // return ['mail', 'database', 'webpush'];
         return ['webpush'];
    }

    // Email notification
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Subscription Reminder')
                    ->line($this->messageText);
    }

    // Database notification
    public function toArray($notifiable)
    {
        return [
            'message' => $this->messageText
        ];
    }

    // WebPush notification
    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
           ->title('Subscription Reminder')
        ->icon('/icon.png') // optional icon
        ->body($this->messageText)
        ->action('View App', url('/dashboard'))
        ->data(['subscription_id' => $this->subscription->id ?? null]);
    }
}
