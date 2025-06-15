<?php

namespace App\Http\Resources;

use App\Services\Avatars\Avatar;
use Illuminate\Http\Request;

class UserResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'language' => $this->language ?: config('app.locale'),
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'avatar' => $this->avatar ? (new Avatar($this->avatar))->getContent(base64: true) : null,
        ];
    }
}
