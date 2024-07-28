<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends Notification
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email', url('/email/verify/' . $notifiable->getKey() . '/' . sha1($notifiable->getEmailForVerification())))
            ->line('If you did not create an account, no further action is required.');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
