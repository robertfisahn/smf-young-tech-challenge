<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_id',
        'invoice_number',
        'date',
        'file_path',
    ];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->payments->sum('amount');
    }

    public function getCurrencyAttribute()
    {
        return $this->payments->first()->currency ?? 'PLN';
    }
}
