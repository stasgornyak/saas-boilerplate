<?php

namespace App\Listeners;

use App\Events\PasswordChanged;
use App\Notifications\PasswordChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendPasswordChangedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PasswordChanged $event): void
    {
        $user = $event->user;

        Notification::route('mail', $user->email)->notify(new PasswordChangedNotification($user));
    }
}
