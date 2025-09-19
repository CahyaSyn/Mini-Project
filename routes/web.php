<?php

use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pages/dashboard', [ViewController::class, 'dashboard'])->name('pages.dashboard');
Route::get('/pages/accounts', [ViewController::class, 'accounts'])->name('pages.accounts');
Route::get('/pages/journals', [ViewController::class, 'journals'])->name('pages.journals');
Route::get('/pages/invoices', [ViewController::class, 'invoices'])->name('pages.invoices');
Route::get('/pages/payments', [ViewController::class, 'payments'])->name('pages.payments');
Route::get('/pages/trial_balance', [ViewController::class, 'trial_balance'])->name('pages.trial_balance');

Route::get('export/trial-balance', [ReportExportController::class, 'exportTrialBalance'])->name('export.trial_balance');
