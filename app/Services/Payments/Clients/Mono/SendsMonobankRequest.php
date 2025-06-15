<?php

namespace App\Services\Payments\Clients\Mono;

use App\Exceptions\LogicException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SendsMonobankRequest
{
    public function __construct(
        private string $url,
        private string $method = 'get',
        private array $params = [],
        private array $headers = []
    ) {
        $baseUrl = config('payments.clients.mono.base_url');
        $this->url = Str::start($this->url, $baseUrl);
        $this->method = Str::lower($this->method);
        $this->params = convertArrayKeys($this->params, 'camel');
    }

    /**
     * @throws LogicException
     */
    public function __invoke(): array
    {
        $response = match ($this->method) {
            'get' => Http::withHeaders($this->headers)->get($this->url, $this->params),
            'post' => Http::withHeaders($this->headers)->post($this->url, $this->params),
            'delete' => Http::withHeaders($this->headers)->delete($this->url),
            default => throw new \InvalidArgumentException('Invalid http method.'),
        };

        if ($response->failed()) {
            if ($response->clientError()) {
                $responseData = $response->json();

                if (isset($responseData['errText'])) {
                    throw new LogicException(
                        message: 'bank_client_error',
                        description: Str::ucfirst($responseData['errText']).' ('.$response->status().').'
                    );
                }

            }

            throw new \RuntimeException('Service currently unavailable.');
        }

        return $response->json();
    }
}
