<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConvertCase extends TransformsRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     */
    public function handle($request, \Closure $next): mixed
    {
        $this->clean($request);

        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = convertArrayKeys($response->getData(true), 'camel');

            $data = $this->formatDescription($data);
            $data = $this->formatMessage($data);

            $response->setData($data);
        }

        return $response;
    }

    /**
     * Transform the given value.
     *
     * @param  string  $keyPrefix
     */
    protected function cleanArray(array $data, $keyPrefix = ''): array
    {
        return convertArrayKeys($data, 'snake');
    }

    protected function stringToCamel(string $string): string
    {
        return Str::replace('.', '', Str::camel($string));
    }

    protected function formatMessage(array $data): array
    {
        if (isset($data['message'])) {
            $data['message'] = is_string($data['message'])
                ? self::stringToCamel($data['message'])
                : collect($data['message'])->map(fn ($item) => self::stringToCamel($item));
        }

        return $data;
    }

    protected function formatDescription(array $data): array
    {
        if (empty($data['description']) && ! empty($data['message'])) {
            $data['description'] = is_string($data['message'])
                ? slugToText($data['message'])
                : implode(' ', array_map(fn ($item) => slugToText($item), $data['message']));
        }

        return $data;
    }
}
