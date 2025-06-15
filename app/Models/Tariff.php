<?php

namespace App\Models;

use App\Casts\AsCurrency;
use App\Enums\LicensePeriod;
use App\Services\_Common\Currency\Currency;

/**
 * Class Tariff.
 *
 * @property int id
 * @property LicensePeriod period
 * @property float price
 * @property Currency currency
 */
class Tariff extends BaseCentralModel
{
    protected $fillable = ['period', 'price', 'currency'];

    public $timestamps = false;

    protected $casts = [
        'period' => LicensePeriod::class,
        'price' => 'float',
        'currency' => AsCurrency::class,
    ];

    // Scopes

    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }
}
