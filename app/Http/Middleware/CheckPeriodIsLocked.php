<?php

namespace App\Http\Middleware;

use App\Models\ClosingPeriode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPeriodIsLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $date = $request->input('posting_date')
            ?: $request->input('invoice_date')
            ?: $request->input('paid_at');

        if (!$date) {
            $model = $request->route()->parameter('journal')
                ?: $request->route()->parameter('invoice')
                ?: $request->route()->parameter('payment');
            if ($model) {
                $date = $model->posting_date ?? $model->invoice_date ?? $model->paid_at;
            }
        }

        if ($date) {
            $period = date('Y-m', strtotime($date));

            $isLocked = ClosingPeriode::where('period', $period)->where('is_locked', true)->exists();

            if ($isLocked) {
                return response()->json(['message' => 'The period ' . $period . ' is locked and cannot be modified.'], 403);
            }
        }

        return $next($request);
    }
}
