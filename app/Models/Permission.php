<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Permission.
 *
 * @property int id
 * @property string name
 * @property string guard_name
 */
class Permission extends SpatiePermission
{
    use TenantConnection;

    public $timestamps = false;
}
