<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RoleResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'sort' => $this->sort,
            'is_system' => $this->is_system,
            'permissions' => collect($this->permissions)
                ->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ];
                }),
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
