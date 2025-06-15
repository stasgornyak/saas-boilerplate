<?php

namespace App\Services\Payments\Clients;

use App\Services\Payments\Clients\Fondy\FondyClient;
use App\Services\Payments\Clients\Mono\MonoClient;
use Illuminate\Support\Facades\App;

class PaymentClientFactory
{
    public static function create(): PaymentClient
    {
        $client = config('payments.default_client');
        $account = App::environment('production') ? 'production' : 'test';

        return match ($client) {
            'fondy' => new FondyClient($account),
            'mono' => new MonoClient($account),
            default => throw new \RuntimeException('Unknown payment client.'),
        };
    }
}
