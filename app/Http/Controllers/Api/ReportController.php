<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function getTrialBalance(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $accounts = ChartOfAccount::select(
            'code',
            'name',
            'normal_balance',
            DB::raw('SUM(CASE WHEN journals.posting_date < ? THEN journal_lines.debit - journal_lines.credit ELSE 0 END) as opening_balance_raw'),
            DB::raw('SUM(CASE WHEN journals.posting_date BETWEEN ? AND ? THEN journal_lines.debit ELSE 0 END) as debit'),
            DB::raw('SUM(CASE WHEN journals.posting_date BETWEEN ? AND ? THEN journal_lines.credit ELSE 0 END) as credit')
        )
            ->leftJoin('journal_lines', 'chart_of_accounts.id', '=', 'journal_lines.account_id')
            ->leftJoin('journals', 'journal_lines.journal_id', '=', 'journals.id')
            ->setBindings([$startDate, $startDate, $endDate, $startDate, $endDate])
            ->groupBy('chart_of_accounts.id', 'code', 'name', 'normal_balance')
            ->orderBy('code')
            ->get()
            ->map(function ($account) {
                // Adjust balances based on the account's normal balance (DR or CR)
                $openingBalance = $account->normal_balance === 'DR' ? $account->opening_balance_raw : $account->opening_balance_raw * -1;

                if ($account->normal_balance === 'DR') {
                    $closingBalance = $openingBalance + $account->debit - $account->credit;
                } else {
                    $closingBalance = $openingBalance - $account->debit + $account->credit;
                }

                return [
                    'code' => $account->code,
                    'name' => $account->name,
                    'opening_balance' => $openingBalance,
                    'debit' => $account->debit,
                    'credit' => $account->credit,
                    'closing_balance' => $closingBalance,
                ];
            });

        return response()->json(['data' => $accounts]);
    }
}
