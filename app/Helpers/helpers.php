<?php

if (! function_exists('slugToText')) {
    function slugToText(string $slug, bool $endWithPeriod = true): string
    {
        $slugWithoutPeriods = str_replace('.', '', $slug);
        $readableSlug = ucfirst(str_replace(['_', '-'], ' ', $slugWithoutPeriods));

        return $endWithPeriod ? $readableSlug.'.' : $readableSlug;
    }

}

if (! function_exists('convertArrayKeys')) {
    function convertArrayKeys(array $array, string $case): array
    {
        if (! in_array($case, ['camel', 'snake', 'studly'])) {
            return [];
        }

        return collect($array)
            ->mapWithKeys(function ($value, $key) use ($case) {
                $key = \Illuminate\Support\Str::$case($key);
                $value = is_array($value) ? convertArrayKeys($value, $case) : $value;

                return [$key => $value];
            })
            ->all();
    }

}

if (! function_exists('convertArrayValues')) {
    function convertArrayValues(array $array, string $case): array
    {
        if (! in_array($case, ['camel', 'snake', 'studly'])) {
            return [];
        }

        return collect($array)
            ->mapWithKeys(function ($value, $key) use ($case) {
                $value = is_string($value) ? \Illuminate\Support\Str::$case($value) : $value;
                $value = is_array($value) ? convertArrayValues($value, $case) : $value;

                return [$key => $value];
            })
            ->all();
    }

    if (! function_exists('jsonResponse')) {
        function jsonResponse(array $data, ?int $code = \Illuminate\Http\Response::HTTP_OK): Illuminate\Http\JsonResponse
        {
            $formattedData = [
                'data' => $data['data'] ?? null,
                'message' => $data['message'] ?? null,
                'description' => $data['description'] ?? null,
            ];

            if (isset($data['meta'])) {
                $formattedData['meta'] = $data['meta'];

            }

            return response()->json($formattedData, $code);
        }

    }

    if (! function_exists('isLocalEnvironment')) {
        function isLocalEnvironment(): bool
        {
            return \Illuminate\Support\Str::startsWith(app()->environment(), 'local');
        }

    }

}
