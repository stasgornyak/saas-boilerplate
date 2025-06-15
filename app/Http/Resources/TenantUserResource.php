<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class TenantUserResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $user = [
            'id' => $this->id,
            'email' => $this->central?->email,
            'first_name' => $this->central?->first_name,
            'last_name' => $this->central?->last_name,
            'language' => $this->central?->language,
            'is_active' => $this->is_active,
            'is_trashed' => $this->is_trashed,
            'is_owner' => $this->central?->pivot && $this->central?->pivot->is_owner,
            'last_activity_at' => $this->formatDate($this->last_activity_at),
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
            'deleted_at' => $this->formatDate($this->deleted_at),
        ];

        if ($role = optional($this->roles)->first()) {
            $user['role'] = [
                'id' => $role->id,
                'name' => $role->name,
            ];

            if ($role->permissions) {
                $user['role']['permissions'] = $role->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ];
                });
            }

        }

        return $user;
    }
}
