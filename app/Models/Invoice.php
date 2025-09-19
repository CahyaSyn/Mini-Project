<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'invoice_date',
        'customer',
        'amount',
        'tax_amount',
        'status',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    protected function totalAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->amount + $this->tax_amount
        );
    }

    protected function amountPaid(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->payments->sum('amount_paid')
        );
    }
}
