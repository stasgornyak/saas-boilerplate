<?php

namespace Database\Seeders;

use App\Models\Tariff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

/*
 * All tariff prices are for development and tests only
 * Real prices should be placed manually into database
 * Seeder doesn't change existing tariffs
 */
class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tariffs = [
            [
                'period' => \App\Enums\LicensePeriod::Monthly,
                'price' => 299,
                'currency' => 'UAH',
            ],
            [
                'period' => \App\Enums\LicensePeriod::Yearly,
                'price' => 2990,
                'currency' => 'UAH',
            ],
        ];

        foreach ($tariffs as $tariff) {
            Tariff::firstOrCreate(Arr::only($tariff, ['period']), $tariff);
        }

    }
}
