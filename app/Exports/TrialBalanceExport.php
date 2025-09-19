<?php

namespace App\Exports;

use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrialBalanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return ChartOfAccount::select(
            'code',
            'name',
            'normal_balance',
            'opening_balance as initial_balance',
            DB::raw('SUM(CASE WHEN journals.posting_date < ? THEN journal_lines.debit - journal_lines.credit ELSE 0 END) as prior_movement'),
            DB::raw('SUM(CASE WHEN journals.posting_date BETWEEN ? AND ? THEN journal_lines.debit ELSE 0 END) as debit'),
            DB::raw('SUM(CASE WHEN journals.posting_date BETWEEN ? AND ? THEN journal_lines.credit ELSE 0 END) as credit')
        )
            ->leftJoin('journal_lines', 'chart_of_accounts.id', '=', 'journal_lines.account_id')
            ->leftJoin('journals', 'journal_lines.journal_id', '=', 'journals.id')
            ->setBindings([$this->startDate, $this->startDate, $this->endDate, $this->startDate, $this->endDate])
            ->groupBy('chart_of_accounts.id', 'code', 'name', 'normal_balance', 'opening_balance')
            ->orderBy('code')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Account Code',
            'Account Name',
            'Opening Balance',
            'Debit',
            'Credit',
            'Closing Balance',
        ];
    }

    public function map($account): array
    {
        $initialBalance = $account->normal_balance === 'CR' ? $account->initial_balance * -1 : $account->initial_balance;
        $openingBalanceForPeriod = $initialBalance + $account->prior_movement;
        if ($account->normal_balance === 'CR') {
            $openingBalanceForPeriod *= -1;
        }

        $debit = (float) $account->debit;
        $credit = (float) $account->credit;

        if ($account->normal_balance === 'DR') {
            $closingBalance = $openingBalanceForPeriod + $debit - $credit;
        } else {
            $closingBalance = $openingBalanceForPeriod - $debit + $credit;
        }

        return [
            $account->code,
            $account->name,
            number_format($openingBalanceForPeriod, 2, '.', ''),
            number_format($debit, 2, '.', ''),
            number_format($credit, 2, '.', ''),
            number_format($closingBalance, 2, '.', ''),
        ];
    }
}
