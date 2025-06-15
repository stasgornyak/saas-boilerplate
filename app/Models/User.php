<?php

namespace App\Models;

use App\Enums\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User.
 *
 * @property int id
 * @property string email
 * @property string password
 * @property string first_name
 * @property string last_name
 * @property Language language
 * @property string avatar
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Tenant[]|Collection
 */
class User extends Authenticatable implements JWTSubject
{
    use CentralConnection;

    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'last_name',
        'language',
        'avatar',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'language' => Language::class,
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    // Relations

    public function tenantsWithInactive(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class)
            ->using(TenantUserPivot::class)
            ->withPivot('is_owner', 'is_active', 'sort');
    }

    public function tenants(): BelongsToMany
    {
        return $this->tenantsWithInactive()->wherePivot('is_active', true);
    }

    public function tenantsInactive(): BelongsToMany
    {
        return $this->tenantsWithInactive()->wherePivot('is_active', false);
    }

    public function tenantsWhereIsOwner(): BelongsToMany
    {
        return $this->tenantsWithInactive()->wherePivot('is_owner', true);
    }

    // Methods

    public function setPassword(): string
    {
        $password = self::generatePassword();
        $this->password = $password;
        $this->save();

        return $password;
    }

    public static function generatePassword(): string
    {
        return Str::random(USER_PASSWORD_LENGTH);
    }

    public function isOwner(Tenant $tenant): bool
    {
        return $this->tenants()
            ->where('tenant_id', $tenant->id)
            ->where('is_owner', true)
            ->exists();
    }

    public function isStaffMember(Tenant $tenant): bool
    {
        return $this->tenants()
            ->where('tenant_id', $tenant->id)
            ->exists();
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function getRoleInTenant(Tenant $tenant): ?Role
    {
        if (! $this->isStaffMember($tenant)) {
            return null;
        }

        tenancy()->initialize($tenant->slug);

        $tenantUser = TenantUser::where('central_id', $this->id)->first();
        $role = $tenantUser->roles()->first();

        tenancy()->end();

        return $role;
    }
}
