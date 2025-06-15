<?php

namespace App\Notifications;

use App\Models\User;

class UserSignedUpNotification extends BaseNotification
{
    public const TEMPLATE_ALIAS = 'user-registered';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, string $password)
    {
        $data = [
            'name' => $user->first_name,
            'email' => $user->email,
            'password' => $password,
            'language' => $user->language,
        ];

        parent::__construct(self::TEMPLATE_ALIAS, $data);
    }
}
