<?php

namespace App\Services\Payments;

use App\Enums\LicenseStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\ForbiddenException;
use App\Exceptions\LogicException;
use App\Models\License;
use App\Models\Payment;
use App\Services\Licenses\LicenseService;
use App\Services\Payments\Clients\PaymentClientFactory;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentService
{
    public function __construct(private readonly LicenseService $licenseService) {}

    public function index(array $data): LengthAwarePaginator
    {
        $filters = $data['filters'] ?? [];
        $pagination = $data['pagination'];

        return Payment::query()
            ->permittedForUser(auth()->user())
            ->whereFilters($filters)
            ->latest()
            ->paginate($pagination['per_page'], ['*'], null, $pagination['page']);
    }

    public function show(int $id): Payment
    {
        return Payment::query()
            ->permittedForUser(auth()->user())
            ->findOrFail($id);
    }

    /**
     * @throws ForbiddenException|LogicException
     */
    public function store(array $data): Payment
    {
        $license = License::query()
            ->permittedForUser(auth()->user())
            ->findOrFail($data['license_id']);

        $this->ensureUserCanPay($license);
        $this->ensureLicenseCanBePaid($license);
        $this->ensureLicenseDoesNotHavePayments($license);

        $data['amount'] = $license->tariff->price;
        $data['currency'] = $license->tariff->currency;

        return Payment::create($data)->refresh();
    }

    /**
     * @throws ForbiddenException|LogicException
     */
    public function getCheckoutUrl(int $id): string
    {
        $payment = Payment::query()
            ->permittedForUser(auth()->user())
            ->with('license')
            ->findOrFail($id);

        if ($payment->status === PaymentStatus::Approved) {
            throw new LogicException('payment_is_already_approved');
        }

        $this->ensureUserCanPay($payment->license);
        $this->ensureLicenseCanBePaid($payment->license);

        return PaymentClientFactory::create()->getCheckout($payment);
    }

    /**
     * @throws ForbiddenException|LogicException
     */
    public function checkPaymentStatus(int $id): Payment
    {
        $payment = Payment::query()
            ->permittedForUser(auth()->user())
            ->with('license')
            ->findOrFail($id);

        $this->ensureUserCanPay($payment->license);

        if (! $payment->ext_id) {
            throw new LogicException('external_id_is_not_set');
        }

        PaymentClientFactory::create()->checkStatus($payment);

        if ($payment->status === PaymentStatus::Approved) {
            $this->licenseService->handlePaymentApproval($payment->license);
        }

        return $payment->refresh();
    }

    public function handleCallback(Request $request): void
    {
        $payment = PaymentClientFactory::create()->handleCallback($request);

        if ($payment->status === PaymentStatus::Approved) {
            $this->licenseService->handlePaymentApproval($payment->license);
        }

    }

    /**
     * TODO: transfer to Gate or Policy?
     *
     * @throws ForbiddenException
     */
    private function ensureUserCanPay(License $license): void
    {
        $tenant = $license->tenant()->first();
        $user = auth()->user();

        if ($user->isOwner($tenant)) {
            return;
        }

        $role = $user->getRoleInTenant($tenant);

        if ($role && $role->code === 'admin') {
            return;
        }

        throw new ForbiddenException('admin_only_can_make_payment');
    }

    /**
     * @throws LogicException
     */
    private function ensureLicenseCanBePaid(License $license): void
    {
        if ($license->status !== LicenseStatus::Created) {
            throw new LogicException('license_can_not_be_paid');
        }

    }

    /**
     * @throws LogicException
     */
    private function ensureLicenseDoesNotHavePayments(License $license): void
    {
        $successfulPaymentsExist = $license
            ->payments()
            ->whereIn('status', PaymentStatus::successfulValues())
            ->exists();

        if ($successfulPaymentsExist) {
            throw new LogicException('license_already_has_payments');
        }

    }
}
