<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChartOfAccount;
use App\Models\Journal;

class JournalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Chart of Accounts are seeded first
        $this->call(ChartOfAccountsSeeder::class);

        $accounts = ChartOfAccount::pluck('id', 'code');

        $journalsData = [
            [
                'header' => ['ref_no' => 'JV-2025-0001', 'posting_date' => '2025-07-01', 'memo' => 'Opening accrual'],
                'lines' => [
                    ['account_id' => $accounts['6101'], 'debit' => 100000.00, 'credit' => 0],
                    ['account_id' => $accounts['2101'], 'debit' => 0, 'credit' => 100000.00],
                ],
            ],
            [
                'header' => ['ref_no' => 'JV-2025-0002', 'posting_date' => '2025-07-15', 'memo' => 'Sales cash'],
                'lines' => [
                    ['account_id' => $accounts['1101'], 'debit' => 2800000.00, 'credit' => 0],
                    ['account_id' => $accounts['4101'], 'debit' => 0, 'credit' => 2800000.00],
                ],
            ],
            [
                'header' => ['ref_no' => 'JV-2025-0003', 'posting_date' => '2025-07-20', 'memo' => 'Utilities expense'],
                'lines' => [
                    ['account_id' => $accounts['5101'], 'debit' => 1200000.00, 'credit' => 0],
                    ['account_id' => $accounts['1101'], 'debit' => 0, 'credit' => 1200000.00],
                ],
            ],
        ];

        DB::transaction(function () use ($journalsData) {
            foreach ($journalsData as $data) {
                $journal = Journal::updateOrCreate(
                    ['ref_no' => $data['header']['ref_no']],
                    $data['header']
                );

                $journal->journalLines()->delete();
                $journal->journalLines()->createMany($data['lines']);
            }
        });
    }
}
