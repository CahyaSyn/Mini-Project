<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    protected $fillable = [
        'ref_no',
        'posting_date',
        'memo',
        'status',
        'created_by',
    ];

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    protected function debitTotal(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->journalLines->sum('debit')
        );
    }

    protected function creditTotal(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->journalLines->sum('credit')
        );
    }
}
