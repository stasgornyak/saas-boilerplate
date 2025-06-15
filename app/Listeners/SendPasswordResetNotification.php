<?php

namespace App\Listeners;

use App\Events\PasswordReset;
use App\Notifications\PasswordResetNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendPasswordResetNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event): void
    {
        $user = $event->user;
        $password = $event->password;

        Notification::route('mail', $user->email)->notify(
            new PasswordResetNotification($user, $password)
        );
    }
}
