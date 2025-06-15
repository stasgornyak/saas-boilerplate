<?php

namespace App\Services\Payments\Clients;

use App\Models\Payment;
use Illuminate\Http\Request;

interface PaymentClient
{
    /**
     * Generates payment url.
     */
    public function getCheckout(Payment $payment): string;

    /**
     * Updates payment status.
     */
    public function checkStatus(Payment $payment): Payment;

    /**
     * Handle payment callback.
     */
    public function handleCallback(Request $request): Payment;
}
