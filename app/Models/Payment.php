<?php

namespace App\Models;

use App\Casts\AsCurrency;
use App\Enums\PaymentStatus;
use App\Models\Traits\Filtering\HasPaymentFilteringScope;
use App\Services\_Common\Currency\Currency;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class Payment.
 *
 * @property int id
 * @property int ext_id
 * @property float amount
 * @property Currency currency
 * @property PaymentStatus status
 * @property string description
 * @property int license_id
 * @property array detail
 * @property int created_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property License license
 * @property User createdBy
 */
class Payment extends BaseCentralModel
{
    use HasPaymentFilteringScope;

    protected $fillable = [
        'ext_id',
        'amount',
        'currency',
        'status',
        'description',
        'license_id',
        'details',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'currency' => AsCurrency::class,
        'status' => PaymentStatus::class,
        'details' => 'array',
    ];

    public static function booted(): void
    {
        parent::booted();

        static::creating(static function (self $payment) {
            $payment->status = PaymentStatus::Created;
            $payment->created_by = $payment->created_by ?: auth()->id();
        });
    }

    // Relations

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes

    public function scopePermittedForUser(Builder $query, User|int $user): void
    {
        $query->whereExists(function ($query) use ($user) {
            $query
                ->select(DB::raw(1))
                ->from('licenses')
                ->whereColumn('licenses.id', 'payments.license_id')
                ->where(function ($query) use ($user) {
                    (new License)->scopePermittedForUser($query, $user);
                });
        });
    }
}
