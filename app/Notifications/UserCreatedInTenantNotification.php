<?php

namespace App\Notifications;

use App\Models\Tenant;
use App\Models\User;

class UserCreatedInTenantNotification extends BaseNotification
{
    public const TEMPLATE_ALIAS = 'user-created';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Tenant $tenant, ?string $password = null)
    {
        $data = [
            'name' => $user->first_name,
            'email' => $user->email,
            'password' => $password,
            'language' => $user->language,
            'tenant_url' => $tenant->getFrontendUrl(),
            'tenant_name' => $tenant->name,
        ];

        parent::__construct(self::TEMPLATE_ALIAS, $data);
    }
}
