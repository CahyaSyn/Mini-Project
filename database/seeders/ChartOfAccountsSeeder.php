<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            ['code' => '1101', 'name' => 'Cash', 'normal_balance' => 'DR'],
            ['code' => '1201', 'name' => 'Accounts Receivable', 'normal_balance' => 'DR'],
            ['code' => '2101', 'name' => 'Accounts Payable', 'normal_balance' => 'CR'],
            ['code' => '4101', 'name' => 'Revenue', 'normal_balance' => 'CR'],
            ['code' => '5101', 'name' => 'Expense', 'normal_balance' => 'DR'],
            ['code' => '6101', 'name' => 'Accrued Expense', 'normal_balance' => 'CR'],
        ];

        // Use upsert to insert or updae seeder is run multiple times.
        ChartOfAccount::upsert(
            $accounts,
            ['code'], // Unique column(s) to check
            ['name', 'normal_balance'] // Columns to update if record exists
        );
    }
}
