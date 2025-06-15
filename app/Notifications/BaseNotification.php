<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $data;

    protected string $template;

    protected string $from;

    protected string $replyTo;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $template, array $data)
    {
        $this->data = $data;

        $language = $data['language'] ?? config('app.locale');
        $this->template = $template.'-'.$language;

        $this->from = config('mail.from.address');
        $this->replyTo = config('mail.reply_to') ?: $this->from;

        $this->data['reply_to'] = $this->replyTo;
        $this->data['base_url'] = config('app.frontend_url');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Resolve this method
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
