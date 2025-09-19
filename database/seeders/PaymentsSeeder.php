<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Invoices are seeded first
        $this->call(InvoicesSeeder::class);

        $invoices = Invoice::whereIn('invoice_no', ['INV-2025-0005', 'INV-2025-0007'])->pluck('id', 'invoice_no');

        if ($invoices->isEmpty()) {
            Log::warning('PaymentsSeeder: No invoices found to create payments for.');
            return;
        }

        $payments = [
            ['invoice_id' => $invoices['INV-2025-0005'], 'payment_ref' => 'PAY-2025-001', 'paid_at' => '2025-07-12', 'amount_paid' => 1000000.00, 'method' => 'Bank Transfer'],
            ['invoice_id' => $invoices['INV-2025-0005'], 'payment_ref' => 'PAY-2025-002', 'paid_at' => '2025-07-22', 'amount_paid' => 800000.00, 'method' => 'Cash'],
            ['invoice_id' => $invoices['INV-2025-0007'], 'payment_ref' => 'PAY-2025-003', 'paid_at' => '2025-07-28', 'amount_paid' => 555000.00, 'method' => 'Bank Transfer'],
        ];

        // Use upsert to avoid duplicates based on the unique 'payment_ref'.
        Payment::upsert(
            $payments,
            ['payment_ref'],
            ['invoice_id', 'paid_at', 'amount_paid', 'method']
        );
    }
}
