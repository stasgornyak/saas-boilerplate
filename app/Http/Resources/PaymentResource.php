<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PaymentResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ext_id' => $this->ext_id,
            'amount' => $this->amount,
            'currency' => $this->currency->toArray(),
            'status' => $this->status,
            'description' => $this->description,
            'license_id' => $this->license_id,
            'details' => $this->details,
            'created_by' => $this->created_by,
            'created_at' => $this->formatDate($this->created_at),
            'updated_at' => $this->formatDate($this->updated_at),
        ];
    }
}
