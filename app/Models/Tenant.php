<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Models\Tenant as StanclTenant;

/**
 * Class Tenant.
 *
 * @property int id
 * @property string slug
 * @property string name
 * @property string avatar
 * @property array settings
 * @property array data
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property User[]|Collection users
 * @property License[]|Collection licenses
 */
class Tenant extends StanclTenant implements TenantWithDatabase
{
    use CentralConnection, HasDatabase;

    protected $casts = [
        'settings' => 'array',
        'data' => 'array',
    ];

    // Tenants

    protected $table = 'tenants';

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'slug',
            'name',
            'avatar',
            'settings',
            'created_at',
            'updated_at',
        ];
    }

    public function getTenantKeyName(): string
    {
        return 'slug';
    }

    public function getIncrementing(): bool
    {
        return true;
    }

    // Relations

    public function usersWithInactive(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(TenantUserPivot::class)
            ->withPivot('is_owner', 'is_active');
    }

    public function users(): BelongsToMany
    {
        return $this->usersWithInactive()->wherePivot('is_active', true);
    }

    public function usersInactive(): BelongsToMany
    {
        return $this->usersWithInactive()->wherePivot('is_active', false);
    }

    public function owner(): BelongsToMany
    {
        return $this->users()->wherePivot('is_owner', true);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    public function activeLicenses(): HasMany
    {
        return $this->licenses()->active();
    }

    // Scopes

    public function scopeWhereUserIsActive($query, User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        $query->whereExists(function ($query) use ($userId) {
            $query
                ->select(DB::raw(1))
                ->from('tenant_user')
                ->whereColumn('tenant_user.tenant_id', 'tenants.id')
                ->where('tenant_user.user_id', $userId)
                ->where('tenant_user.is_active', true);
        });
    }

    public function scopeWhereUserIsOwner($query, User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        $query->whereExists(function ($query) use ($userId) {
            $query
                ->select(DB::raw(1))
                ->from('tenant_user')
                ->whereColumn('tenant_user.tenant_id', 'tenants.id')
                ->where('tenant_user.user_id', $userId)
                ->where('tenant_user.is_owner', true);
        });
    }

    public function scopeWhereUserIsNotOwner($query, User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : $user;

        $query->whereExists(function ($query) use ($userId) {
            $query
                ->select(DB::raw(1))
                ->from('tenant_user')
                ->whereColumn('tenant_user.tenant_id', 'tenants.id')
                ->where('tenant_user.user_id', $userId)
                ->where('tenant_user.is_owner', false);
        });
    }

    // Methods

    public function attachUser(User $user, bool $isOwner = false): void
    {
        $sort = $this->users()->withPivot(['sort'])->max('sort') + SORT_SECOND_MODIFIER;

        $this->users()->attach($user->id, [
            'is_owner' => $isOwner,
            'sort' => $sort,
        ]);
    }

    public function detachUser(User $user): void
    {
        $this->users()->detach($user->id);
    }
}
