<?php

use App\Http\Controllers\Api\ChartOfAccountController;
use App\Http\Controllers\Api\ClosingPeriodeController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('accounts', ChartOfAccountController::class);
Route::apiResource('journals', JournalController::class);
Route::apiResource('invoices', InvoiceController::class);
Route::apiResource('payments', PaymentController::class);
Route::apiResource('closing_periods', ClosingPeriodeController::class);

Route::get('pages/trial-balance', [ReportController::class, 'getTrialBalance'])->name('pages.trial_balance');
