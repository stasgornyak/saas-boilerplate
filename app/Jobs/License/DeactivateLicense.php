<?php

namespace App\Jobs\License;

use App\Enums\LicenseStatus;
use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeactivateLicense implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly License $license) {}

    public function handle(): void
    {
        if ($this->license->status === LicenseStatus::Active && $this->license->isExpired()) {
            $this->license->update(['status' => LicenseStatus::Expired]);
        }

    }
}
