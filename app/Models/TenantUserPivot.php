<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * Class SpaceUserPivot.
 *
 * @property int space_id
 * @property int user_id
 * @property bool is_owner
 * @property bool is_active
 * @property int sort
 */
class TenantUserPivot extends Pivot
{
    use CentralConnection;

    protected $table = 'tenant_user';

    protected $casts = [
        'is_owner' => 'boolean',
        'is_active' => 'boolean',
    ];

    public $timestamps = false;
}
