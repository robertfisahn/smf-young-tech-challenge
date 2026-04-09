<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tax_id', 'address'];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
