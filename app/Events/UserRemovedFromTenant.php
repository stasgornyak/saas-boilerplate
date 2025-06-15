<?php

namespace App\Events;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRemovedFromTenant
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;

    public Tenant $tenant;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Tenant $tenant)
    {
        $this->user = $user;
        $this->tenant = $tenant;
    }
}
