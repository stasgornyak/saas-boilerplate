<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class TenantUser.
 *
 * @property int id
 * @property int central_id
 * @property bool is_active
 * @property bool is_trashed
 * @property Carbon last_activity_at
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class TenantUser extends Authenticatable
{
    use HasRoles, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'central_id',
        'is_active',
        'last_activity_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_active' => 'boolean',
        'is_trashed' => 'boolean',
    ];

    protected $appends = ['is_trashed'];

    public function guardName(): string
    {
        return 'tenant_api';
    }

    protected function getDefaultGuardName(): string
    {
        return 'tenant_api';
    }

    // Attributes

    protected function isTrashed(): Attribute
    {
        return Attribute::get(function ($value, array $attributes) {
            return (bool) $attributes['deleted_at'] ?? false;
        });
    }
}
