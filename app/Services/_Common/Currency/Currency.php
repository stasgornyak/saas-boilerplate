<?php

namespace App\Services\_Common\Currency;

use Illuminate\Support\Str;

class Currency
{
    private string $code;

    private string $name;

    private ?int $codeIso;

    private string $symbol;

    private bool $isCrypto;

    private ?int $sort;

    private static array $currencies = [];

    public function __construct(string $code)
    {
        $data = self::getCurrencies()[strtoupper($code)]
            ?? throw new \RuntimeException('Invalid currency');

        $this->code = $data['code'];
        $this->name = $data['name'];
        $this->codeIso = $data['code_iso'] ?? null;
        $this->symbol = $data['symbol'];
        $this->isCrypto = $data['is_crypto'] ?? false;
        $this->sort = $data['sort'] ?? null;
    }

    public function __get(string $name)
    {
        $name = Str::camel($name);

        return property_exists(self::class, $name)
            ? $this->$name
            : null;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'symbol' => $this->symbol,
        ];
    }

    private static function getCurrencies()
    {
        if (! self::$currencies) {
            self::$currencies = config('currencies');
        }

        return self::$currencies;
    }
}
