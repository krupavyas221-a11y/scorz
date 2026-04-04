<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PinResetNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('pin.reset.form', [
            'token' => $this->token,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->subject('Reset Your Scorz PIN')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You requested a PIN reset for your Scorz account.')
            ->action('Reset PIN', $url)
            ->line('This link will expire in 60 minutes.')
            ->line('If you did not request this, no action is needed.')
            ->salutation('— The Scorz Team');
    }
}
