<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Notifications\UserSignedUpNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendUserSignedUpNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        $password = $event->password;

        Notification::route('mail', $user->email)->notify(
            new UserSignedUpNotification($user, $password)
        );
    }
}
