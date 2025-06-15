<?php

namespace App\Listeners;

use App\Events\UserRemovedFromTenant;
use App\Notifications\UserRemovedFromTenantNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendUserRemovedFromTenantNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(UserRemovedFromTenant $event): void
    {
        $user = $event->user;
        $tenant = $event->tenant;

        Notification::route('mail', $user->email)->notify(
            new UserRemovedFromTenantNotification($user, $tenant)
        );
    }
}
