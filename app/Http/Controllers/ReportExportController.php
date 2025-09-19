<?php

namespace App\Http\Controllers;

use App\Exports\TrialBalanceExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    public function exportTrialBalance(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $fileName = 'Trial_Balance_' . $request->start_date . '_to_' . $request->end_date . '.xlsx';

        return Excel::download(new TrialBalanceExport($request->start_date, $request->end_date), $fileName);
    }
}
