<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function dashboard()
    {
        return view('pages.dashboard');
    }

    public function accounts()
    {

        return view('pages.accounts.index');
    }

    public function journals()
    {
        return view('pages.journals.index');
    }

    public function invoices()
    {
        return view('pages.invoices.index');
    }
    public function payments()
    {
        return view('pages.payments.index');
    }
    public function trial_balance()
    {
        return view('pages.trial_balance.index');
    }
}
