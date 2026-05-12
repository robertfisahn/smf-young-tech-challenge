<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contractor_id',
        'invoice_number',
        'date',
        'file_path',
    ];

    /**
     * @return BelongsTo<Contractor, Invoice>
     */
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    /**
     * @return HasMany<InvoiceItem>
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * @return HasMany<Payment>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->payments->sum('amount');
    }

    public function getCurrencyAttribute(): string
    {
        return $this->payments->first()?->currency ?? 'PLN';
    }
}
