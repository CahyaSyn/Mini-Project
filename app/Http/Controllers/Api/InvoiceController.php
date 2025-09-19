<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Invoice;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with('payments')->orderByDesc('invoice_date')->get();
        return response()->json(['data' => $invoices]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_no' => 'required|string|unique:invoices|max:20',
            'invoice_date' => 'required|date',
            'customer' => 'required|string|max:120',
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:open,partial,paid',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $invoice = Invoice::create($validator->validated());
        return response()->json($invoice, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load('payments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'invoice_no' => 'required|string|unique:invoices,invoice_no,' . $invoice->id . '|max:20',
            'invoice_date' => 'required|date',
            'customer' => 'required|string|max:120',
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:open,partial,paid',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            DB::beginTransaction();

            $invoice->update($validator->validated());
            $invoiceTotal = $invoice->amount + ($invoice->tax_amount ?? 0);

            $arAccount = ChartOfAccount::where('code', '1201')->firstOrFail();
            $revenueAccount = ChartOfAccount::where('code', '4101')->firstOrFail();

            $journal = Journal::create([
                'posting_date' => $invoice->invoice_date,
                'ref_no' => 'INV-' . $invoice->invoice_no,
                'memo' => 'Invoice to ' . $invoice->customer,
            ]);

            $journal->journalLines()->createMany([
                ['account_id' => $arAccount->id, 'debit' => $invoiceTotal, 'credit' => 0],
                ['account_id' => $revenueAccount->id, 'debit' => 0, 'credit' => $invoiceTotal],
            ]);

            DB::commit();

            return response()->json($invoice, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update invoice.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->noContent();
    }
}
