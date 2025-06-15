<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Role.
 *
 * @property int id
 * @property string name
 * @property string code
 * @property string guard_name
 * @property bool is_system
 * @property int sort
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Role extends SpatieRole
{
    use TenantConnection;

    protected $casts = [
        'is_system' => 'boolean',
    ];

    // Scopes

    public function scopeSystem($query): void
    {
        $query->where('is_system', true);
    }

    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            TenantUser::class,
            'model',
            config('permission.table_names.model_has_roles'),
            app(PermissionRegistrar::class)->pivotRole,
            config('permission.column_names.model_morph_key')
        );
    }
}
