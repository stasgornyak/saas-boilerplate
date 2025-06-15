<?php

namespace App\Models;

use App\Enums\LicenseStatus;
use App\Models\Traits\Filtering\HasLicenseFilteringScope;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class License.
 *
 * @property int id
 * @property int tenant_id
 * @property int tariff_id
 * @property Carbon valid_from
 * @property Carbon valid_to
 * @property LicenseStatus status
 * @property int created_by
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Tenant tenant
 * @property Tariff tariff
 * @property User createdBy
 */
class License extends BaseCentralModel
{
    use HasLicenseFilteringScope;

    protected $fillable = [
        'tenant_id',
        'tariff_id',
        'valid_from',
        'valid_to',
        'status',
        'created_by',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'status' => LicenseStatus::class,
    ];

    protected $with = ['tariff'];

    public static function booted(): void
    {
        parent::booted();

        static::creating(static function (self $payment) {
            $payment->status = $payment->status ?: LicenseStatus::Created;
            $payment->created_by = $payment->created_by ?: auth()->id();
        });
    }

    // Relations

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
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
                ->from('tenants')
                ->whereColumn('tenants.id', 'licenses.tenant_id')
                ->where(function ($query) use ($user) {
                    (new Tenant)->scopeWhereUserIsActive($query, $user);
                });
        });
    }

    public function scopeActive($builder)
    {
        $now = now();

        return $builder
            ->where('valid_from', '<=', $now)
            ->where('valid_to', '>=', $now)
            ->where('status', LicenseStatus::Active);
    }

    // Methods

    public function isExpired(): bool
    {
        return $this->valid_to->lt(now());
    }

    public function isValid(): bool
    {
        return now()->between($this->valid_from, $this->valid_to);
    }
}
