<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $account = ChartOfAccount::orderBy('code')->get();
        return response()->json(['data' => $account]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:chart_of_accounts|max:10',
            'name' => 'required|string|max:100',
            'normal_balance' => 'required|in:DR,CR',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $account = ChartOfAccount::create($validator->validated());

        return response()->json($account, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ChartOfAccount $account)
    {
        return response()->json(['data' => $account]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChartOfAccount $account)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|max:10|unique:chart_of_accounts,code,' . $account->id,
            'name' => 'required|string|max:100',
            'normal_balance' => 'required|in:DR,CR',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $account->update($validator->validated());

        return response()->json($account);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChartOfAccount $account)
    {
        $account->delete();
        return response()->noContent();
    }
}
