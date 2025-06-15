<?php

namespace App\Events;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAddedToTenant
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;

    public Tenant $tenant;

    public ?string $password;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Tenant $tenant, ?string $password = null)
    {
        $this->user = $user;
        $this->tenant = $tenant;
        $this->password = $password;
    }
}
