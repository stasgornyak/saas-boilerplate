<?php

namespace App\Services\Payments\Clients\Mono;

use App\Enums\PaymentStatus;
use App\Exceptions\LogicException;
use App\Models\Payment;
use App\Services\Payments\Clients\PaymentClient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class MonoClient implements PaymentClient
{
    public const ENDPOINTS = [
        'get_checkout' => [
            'url' => '/api/merchant/invoice/create',
            'method' => 'post',
        ],
        'check_status' => [
            'url' => '/api/merchant/invoice/status',
            'method' => 'get',
        ],
        'get_public_key' => [
            'url' => '/api/merchant/pubkey',
            'method' => 'get',
        ],
    ];

    public const STATUSES_MAP = [
        'created' => PaymentStatus::Created,
        'processing' => PaymentStatus::Processing,
        'failure' => PaymentStatus::Declined,
        'hold' => PaymentStatus::Hold,
        'success' => PaymentStatus::Approved,
        'expired' => PaymentStatus::Expired,
        'reversed' => PaymentStatus::Reversed,
    ];

    private array $merchantConfig;

    public function __construct(string $account = 'test')
    {
        $this->merchantConfig = config("payments.clients.mono.accounts.$account");

        if (! isset($this->merchantConfig['token'])) {
            throw new \RuntimeException('Merchant not set.');
        }

    }

    /**
     * @throws LogicException
     */
    public function getCheckout(Payment $payment): string
    {
        if (! ($user = $payment->createdBy()->first())) {
            throw new \RuntimeException('User not set.');
        }

        $url = self::ENDPOINTS['get_checkout']['url'];
        $method = self::ENDPOINTS['get_checkout']['method'];
        $headers = $this->formHeaders();

        $amountInCents = (int) ($payment->amount * MINOR_UNITS_IN_CURRENCY);
        $currencyCodeIso = $payment->currency->code_iso;

        $callbackConfig = config('payments.callbacks');

        $frontendUrl = config('app.frontend_url').$callbackConfig['frontend_path'];
        $backendUrl = url(route($callbackConfig['backend_route']));

        $params = [
            'amount' => $amountInCents,
            'ccy' => $currencyCodeIso,
            'merchantPaymInfo' => [
                'reference' => (string) $payment->license_id,
                'destination' => $payment->description,
                'customerEmails' => [$user->email],
            ],
            'redirectUrl' => $frontendUrl,
            'webHookUrl' => $backendUrl,
        ];

        $responseData = (new SendsMonobankRequest($url, $method, $params, $headers))();

        $payment->update(['ext_id' => $responseData['invoiceId']]);

        return $responseData['pageUrl'];
    }

    /**
     * @throws LogicException
     */
    public function checkStatus(Payment $payment): Payment
    {
        $params = [
            'invoiceId' => $payment->ext_id,
        ];

        $url = self::ENDPOINTS['check_status']['url'];
        $method = self::ENDPOINTS['check_status']['method'];
        $headers = $this->formHeaders();

        $responseData = (new SendsMonobankRequest($url, $method, $params, $headers))();

        $modifiedAt = Carbon::parse($responseData['modifiedDate'])->setTimezone('UTC');

        if ($modifiedAt > $payment->updated_at) {
            $status = self::STATUSES_MAP[$responseData['status']]
                ?? throw new \RuntimeException('Unknown status.');

            $payment->update(['status' => $status]);
        }

        return $payment;
    }

    /**
     * @throws LogicException
     */
    public function handleCallback(Request $request): Payment
    {
        $this->verifyWebhookSignature($request);

        $requestData = $request->all();

        if (! isset($requestData['invoiceId'], $requestData['status'], $requestData['modifiedDate'])) {
            throw new \RuntimeException('Response not valid.');
        }

        $payment = Payment::firstWhere('ext_id', $requestData['invoiceId']);

        if (! $payment) {
            throw new \RuntimeException('Payment not found.');
        }

        $modifiedAt = Carbon::parse($requestData['modifiedDate'])->setTimezone('UTC');

        if ($modifiedAt > $payment->updated_at) {
            $status = self::STATUSES_MAP[$requestData['status']]
                ?? throw new \RuntimeException('Unknown status.');

            $payment->update(['status' => $status]);
        }

        return $payment;
    }

    private function formHeaders(): array
    {
        return [
            'X-Token' => $this->merchantConfig['token'],
        ];
    }

    /**
     * @throws LogicException
     */
    private function verifyWebhookSignature(Request $request): void
    {
        $message = $request->getContent();
        $xSignBase64 = $request->header('X-Sign');

        $pubKeyBase64 = $this->getPublicKey();

        if ($this->isSignatureValid($pubKeyBase64, $xSignBase64, $message)) {
            return;
        }

        $pubKeyBase64 = $this->getPublicKey(useCache: false);

        if ($this->isSignatureValid($pubKeyBase64, $xSignBase64, $message)) {
            return;
        }

        throw new LogicException('Signature not valid.');
    }

    private function isSignatureValid(string $pubKeyBase64, string $xSignBase64, string $message): bool
    {
        $signature = base64_decode($xSignBase64);
        $publicKey = openssl_get_publickey(base64_decode($pubKeyBase64));

        $result = openssl_verify($message, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        return $result === 1;
    }

    /**
     * @throws LogicException
     */
    private function getPublicKey(bool $useCache = true): string
    {
        if ($useCache) {
            $publicKey = Cache::get('monobank_public_key');

            if ($publicKey) {
                return $publicKey;
            }

        }

        $url = self::ENDPOINTS['get_public_key']['url'];
        $method = self::ENDPOINTS['get_public_key']['method'];
        $headers = $this->formHeaders();

        $responseData = (new SendsMonobankRequest($url, $method, [], $headers))();

        if (! isset($responseData['key'])) {
            throw new \RuntimeException('Response not valid.');
        }

        Cache::put('monobank_public_key', $responseData['key']);

        return $responseData['key'];
    }
}
