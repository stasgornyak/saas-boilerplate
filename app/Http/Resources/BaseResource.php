<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    protected function formatDate($date, ?string $format = null): ?string
    {
        if ($date) {
            return $format ? $date->format($format) : $date->toDateTimeString();
        }

        return null;
    }
}
