<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class LicenseBriefResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'valid_from' => $this->formatDate($this->valid_from),
            'valid_to' => $this->formatDate($this->valid_to),
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
            'tariff' => $this->tariff
                ? [
                    'id' => $this->tariff->id,
                    'period' => $this->tariff->period,
                    'price' => $this->tariff->price,
                    'currency' => $this->tariff->currency->toArray(),
                    'is_active' => $this->tariff->is_active,
                ]
                : null,
        ];
    }
}
