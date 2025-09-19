<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClosingPeriode;
use App\Models\Journal;
use App\PeriodLockCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JournalController extends Controller
{
    use PeriodLockCheck;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $journals = Journal::with('journalLines.account')
            ->orderBy('posting_date', 'desc')
            ->get();

        return response()->json(['data' => $journals]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPeriodIsLocked($request->posting_date);
        $validator = Validator::make($request->all(), [
            'posting_date' => 'required|date',
            'memo' => 'required|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Validate debits = credits
        $totalDebit = collect($request->lines)->sum('debit');
        $totalCredit = collect($request->lines)->sum('credit');

        if ($totalDebit !== $totalCredit) {
            return response()->json(['message' => 'Total debit dan credit harus sama.'], 422);
        }

        // Use a database transaction to ensure data integrity
        try {
            DB::beginTransaction();

            $journal = Journal::create([
                'posting_date' => $request->posting_date,
                'ref_no' => 'JV-' . time(),
                'memo' => $request->memo,
            ]);

            foreach ($request->lines as $line) {
                $journal->journalLines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                ]);
            }

            DB::commit();

            return response()->json($journal->load('journalLines.account'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menambahkan journal.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Journal $journal)
    {
        return response()->json($journal->load('journalLines.account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Journal $journal)
    {
        $this->checkPeriodIsLocked($request->posting_date);
        $validator = Validator::make($request->all(), [
            'posting_date' => 'required|date',
            'memo' => 'required|string|max:255',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Validate debits = credits
        $totalDebit = collect($request->lines)->sum('debit');
        $totalCredit = collect($request->lines)->sum('credit');

        if ($totalDebit !== $totalCredit) {
            return response()->json(['message' => 'Total debit dan credit harus sama.'], 422);
        }

        try {
            DB::beginTransaction();

            $journal->update($request->only(['posting_date', 'memo']));

            $journal->journalLines()->delete();

            foreach ($request->lines as $line) {
                $journal->journalLines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                ]);
            }

            DB::commit();

            return response()->json($journal->load('journalLines.account'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menambahkan journal.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Journal $journal)
    {
        $this->checkPeriodIsLocked($journal->posting_date);
        $journal->delete();

        return response()->noContent();
    }
}
