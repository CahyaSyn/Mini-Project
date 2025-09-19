<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with('invoice')->orderBy('paid_at', 'desc')->get();
        return response()->json(['data' => $payments]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'paid_at' => 'required|date',
            'amount_paid' => 'required|numeric|min:0.01',
            'method' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $payment = DB::transaction(function () use ($request) {
                $invoice = Invoice::findOrFail($request->invoice_id);

                // Calculate invoice total and current amount paid
                $invoiceTotal = $invoice->amount + $invoice->tax_amount;
                $currentPaid = $invoice->payments()->sum('amount_paid');
                $balanceDue = $invoiceTotal - $currentPaid;

                // Prevent overpayment
                if ($request->amount_paid > $balanceDue + 0.001) {
                    throw new \Exception('Payment amount cannot be greater than the balance due.');
                }

                $paymentDate = $request->paid_at;
                $year = date('Y', strtotime($paymentDate));
                $prefix = 'PAY-' . $year . '-';

                // Find the latest payment with this year prefix
                $latestThisYear = Payment::where('payment_ref', 'LIKE', $prefix . '%')->latest('payment_ref')->first();

                $nextNumber = 1;
                if ($latestThisYear) {
                    // Ambil 3 digit terakhir sebagai nomor urut
                    $lastNumber = (int) substr($latestThisYear->payment_ref, -3);
                    $nextNumber = $lastNumber + 1;
                }

                $paymentRef = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

                $newPayment = $invoice->payments()->create([
                    'paid_at' => $request->paid_at,
                    'amount_paid' => $request->amount_paid,
                    'method' => $request->method,
                    'payment_ref' => $paymentRef,
                ]);

                $newTotalPaid = $currentPaid + $newPayment->amount_paid;

                if ($newTotalPaid >= $invoiceTotal) {
                    $invoice->status = 'paid';
                } else {
                    $invoice->status = 'partial';
                }
                $invoice->save();

                $cashAccount = ChartOfAccount::where('code', '1101')->firstOrFail();
                $arAccount = ChartOfAccount::where('code', '1201')->firstOrFail();

                $journal = Journal::create([
                    'posting_date' => $newPayment->paid_at,
                    'ref_no' => $newPayment->payment_ref,
                    'memo' => 'Payment for invoice ' . $invoice->invoice_no,
                ]);

                $journal->journalLines()->createMany([
                    ['account_id' => $cashAccount->id, 'debit' => $newPayment->amount_paid, 'credit' => 0],
                    ['account_id' => $arAccount->id, 'debit' => 0, 'credit' => $newPayment->amount_paid],
                ]);

                return $newPayment;
            });

            return response()->json($payment, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return response()->json($payment->load('invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        try {
            DB::transaction(function () use ($payment) {
                $invoice = $payment->invoice;

                $payment->delete();

                $totalPaid = $invoice->payments()->sum('amount_paid');
                $invoiceTotal = $invoice->amount + $invoice->tax_amount;

                if ($totalPaid <= 0) {
                    $invoice->status = 'open';
                } elseif ($totalPaid < $invoiceTotal) {
                    $invoice->status = 'partial';
                } else {
                    $invoice->status = 'paid';
                }
                $invoice->save();
            });

            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete payment.', 'error' => $e->getMessage()], 500);
        }
    }
}
