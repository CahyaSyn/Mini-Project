<?php

namespace App;

use App\Models\ClosingPeriode;
use Illuminate\Validation\ValidationException;

trait PeriodLockCheck
{
    protected function checkPeriodIsLocked(string $date): void
    {
        $period = date('Y-m', strtotime($date));

        $isLocked = ClosingPeriode::where('period', $period)->where('is_locked', true)->exists();

        if ($isLocked) {
            throw ValidationException::withMessages([
                'period' => 'The period ' . $period . ' is locked and cannot be modified.',
            ]);
        }
    }
}
