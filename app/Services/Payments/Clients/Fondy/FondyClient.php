<?php

namespace App\Services\Payments\Clients\Fondy;

use App\Models\Payment;
use App\Services\Payments\Clients\PaymentClient;
use Cloudipsp\Checkout;
use Cloudipsp\Configuration;
use Cloudipsp\Exception\ApiException;
use Cloudipsp\Order;
use Cloudipsp\Result\Result;
use Illuminate\Http\Request;

/**
 * Install to use this module:
 * composer require cloudipsp/php-sdk-v2
 */
class FondyClient implements PaymentClient
{
    public function __construct(string $account = 'test')
    {
        $merchantConfig = config("payments.clients.fondy.accounts.$account");

        if (! isset($merchantConfig['merchant_id'], $merchantConfig['secret_key'])) {
            throw new \RuntimeException('Merchant not set.');
        }

        Configuration::setMerchantId($merchantConfig['merchant_id']);
        Configuration::setSecretKey($merchantConfig['secret_key']);
    }

    public function getCheckout(Payment $payment): string
    {
        $amountInCents = (int) ($payment->amount * 100);
        $currencyCode = $payment->currency;

        if (! ($user = $payment->createdBy()->first())) {
            throw new \RuntimeException('User not set.');
        }

        $callbackConfig = config('payments.callbacks');

        $frontendUrl = config('app.frontend_url').$callbackConfig['frontend_path'];
        $backendUrl = url(route($callbackConfig['backend_route']));

        $data = [
            'amount' => $amountInCents,
            'currency' => $currencyCode,
            'order_desc' => $payment->description,
            'order_id' => $payment->ext_id,
            'sender_email' => $user->email,
            'lang' => $user->language,
            'response_url' => $frontendUrl,
            'server_callback_url' => $backendUrl,
        ];

        try {
            $response = Checkout::url($data);
            $payment->update(['ext_id' => $response->getOrderID()]);
        } catch (ApiException $e) {
            throw new \RuntimeException($e->getMessage().' ('.$e->getFondyCode().').');
        }

        return $response->getUrl();
    }

    public function checkStatus(Payment $payment): Payment
    {
        $requestData = [
            'order_id' => $payment->ext_id,
        ];

        try {
            $response = Order::status($requestData);
            $responseData = $response->getData();

            $payment->update(['status' => $responseData['order_status']]);
        } catch (ApiException $e) {
            throw new \RuntimeException($e->getMessage().' ('.$e->getFondyCode().').');
        }

        return $payment;
    }

    public function handleCallback(Request $request): Payment
    {
        $requestData = $request->all();

        try {
            $result = new Result($requestData);
            $data = $result->getData();

            if (! $result->isValid($data)) {
                throw new \RuntimeException('Response not valid.');
            }

            if ($data['response_status'] === 'failure') {
                throw new \RuntimeException($data['error_message'].' ('.$data['error_code'].').');
            }

            $payment = Payment::where('ext_id', $data['order_id'])->first();

            if (! $payment) {
                throw new \RuntimeException('Payment not found.');
            }

            $payment->update(['status' => $data['order_status']]);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }

        return $payment;
    }
}
