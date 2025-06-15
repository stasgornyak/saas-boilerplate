<?php

namespace App\Listeners;

use App\Events\UserAddedToTenant;
use App\Notifications\UserAddedToTenantNotification;
use App\Notifications\UserCreatedInTenantNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendUserAddedToTenantNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserAddedToTenant $event): void
    {
        $user = $event->user;
        $tenant = $event->tenant;
        $password = $event->password;

        if ($password) {
            Notification::route('mail', $user->email)->notify(
                new UserCreatedInTenantNotification($user, $tenant, $password)
            );

            return;
        }

        Notification::route('mail', $user->email)->notify(
            new UserAddedToTenantNotification($user, $tenant)
        );
    }
}
