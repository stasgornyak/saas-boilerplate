<?php

return [
    'permissions' => [
        [
            'name' => 'settings',
            'guard_name' => 'tenant_api',
        ],
    ],

    'roles' => [
        [
            'name' => 'Administrator',
            'code' => 'admin',
            'is_system' => true,
            'permissions' => 'all',
            'guard_name' => 'tenant_api',
        ], [
            'name' => 'Staff member',
            'code' => 'staff',
            'is_system' => false,
            'permissions' => [

            ],
            'guard_name' => 'tenant_api',
        ],
    ],
];
