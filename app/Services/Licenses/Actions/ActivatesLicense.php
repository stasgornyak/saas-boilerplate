<?php

namespace App\Services\Licenses\Actions;

use App\Enums\LicenseStatus;
use App\Jobs\License\ActivateLicense;
use App\Jobs\License\DeactivateLicense;
use App\Models\License;
use Illuminate\Support\Carbon;

class ActivatesLicense
{
    public function __construct(private License $license, private ?Carbon $dateFrom = null, private ?int $period = null) {}

    public function __invoke(): License
    {
        $validFrom = $this->dateFrom ? $this->dateFrom->copy() : now();

        $period = $this->license->tariff ? $this->license->tariff->period->value : $this->period;
        $validTo = $validFrom->copy()->addDays($period);

        $status = $this->dateFrom ? LicenseStatus::Paid : LicenseStatus::Active;

        $this->license->update([
            'valid_from' => $validFrom,
            'valid_to' => $validTo,
            'status' => $status,
        ]);

        if ($this->dateFrom) {
            ActivateLicense::dispatch($this->license)->delay($validFrom->copy()->addSecond());
        }

        DeactivateLicense::dispatch($this->license)->delay($validTo->copy()->addSecond());

        return $this->license;
    }
}
