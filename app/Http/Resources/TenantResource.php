<?php

namespace App\Http\Resources;

use App\Services\Avatars\Avatar;
use Illuminate\Http\Request;

class TenantResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'avatar' => $this->avatar ? (new Avatar($this->avatar))->getContent(base64: true) : null,
            'settings' => $this->settings,
        ];

        if (isset($this->pivot)) {
            $data['is_owner'] = $this->pivot->is_owner;
            $data['sort'] = $this->pivot->sort;
        }

        if ($this->relationLoaded('activeLicenses')) {
            $data['license'] = $this->getActiveLicense();
        }

        $data['created_at'] = $this->formatDate($this->created_at);
        $data['updated_at'] = $this->formatDate($this->updated_at);

        return $data;
    }

    private function getActiveLicense(): ?array
    {
        if ($this->activeLicenses->isNotEmpty()) {
            $license = $this->activeLicenses->first();

            return [
                'id' => $license->id,
                'valid_from' => $this->formatDate($license->valid_from),
                'valid_to' => $this->formatDate($license->valid_to),
            ];
        }

        return null;
    }
}
