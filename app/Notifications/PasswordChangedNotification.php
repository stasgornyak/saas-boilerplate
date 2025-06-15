<?php

namespace App\Notifications;

use App\Models\User;

class PasswordChangedNotification extends BaseNotification
{
    public const TEMPLATE_ALIAS = 'password-changed';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $data = [
            'name' => $user->first_name,
            'email' => $user->email,
            'language' => $user->language,
        ];

        parent::__construct(self::TEMPLATE_ALIAS, $data);
    }
}
