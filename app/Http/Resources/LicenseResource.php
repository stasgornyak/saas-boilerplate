<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class LicenseResource extends BaseResource
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
            'payments' => $this->payments
                ? $this->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'currency' => $payment->currency->toArray(),
                        'status' => $payment->status,
                        'description' => $payment->descxription,
                        'created_by' => $payment->created_by,
                        'created_at' => $this->formatDate($payment->created_at),
                        'updated_at' => $this->formatDate($payment->updated_at),
                    ];
                })
                : null,
        ];
    }
}
