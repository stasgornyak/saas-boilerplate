<?php

namespace App\Services\Tenants\Actions;

use App\Exceptions\ForbiddenException;
use App\Exceptions\LogicException;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Services\Avatars\Avatar;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class UpdatesTenant
{
    public function __construct(private Tenant $tenant, private array $data, private Authenticatable $user) {}

    /**
     * @throws ForbiddenException|TenantCouldNotBeIdentifiedById
     * @throws LogicException
     */
    public function __invoke(): Tenant
    {
        $this->ensureUserCanUpdateTenant();
        $this->updateTenant();

        return $this->tenant;
    }

    /**
     * @throws ForbiddenException|TenantCouldNotBeIdentifiedById
     */
    private function ensureUserCanUpdateTenant(): void
    {
        tenancy()->initialize($this->tenant->slug);

        $permissions = TenantUser::where('central_id', $this->user->id)
            ->first()
            ->roles()
            ->with('permissions:id,name')
            ->first()
            ->permissions()
            ->pluck('name');

        if (! $permissions->contains('settings')) {
            throw new ForbiddenException;
        }

        tenancy()->end();
    }

    /**
     * @throws LogicException
     */
    private function updateTenant(): void
    {
        if (array_key_exists('avatar', $this->data)) {
            if ($this->data['avatar']) {
                if ($this->tenant->avatar) {
                    (new Avatar($this->tenant->avatar))->delete();
                }

                $this->data['avatar'] = Avatar::upload($this->data['avatar'])->getFileName();
            }

            if (is_null($this->data['avatar']) && $this->tenant->avatar) {
                (new Avatar($this->tenant->avatar))->delete();
            }

        }

        if (isset($this->data['slug'])) {
            $newSlug = Str::slug($this->data['slug']);

            if (Tenant::where('slug', $newSlug)->where('id', '!=', $this->tenant->id)->exists()) {
                throw new LogicException('slug_has_already_been_taken');
            }

            $this->data['slug'] = $newSlug;
        }

        $this->tenant->update($this->data);
        $this->tenant->refresh();
    }
}
