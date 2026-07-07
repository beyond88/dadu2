<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lc;
use App\Models\LcExpense;
use App\DataTables\LcDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LcController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(['permission:LC List|LC Create|LC Edit|LC Delete']);
    // }

    public function index(LcDataTable $dataTable)
    {
        set_page_meta(__('custom.lcs'));
        return $dataTable->render('admin.lcs.index');
    }

    public function create()
    {
        set_page_meta(__('custom.add_lc'));
        return view('admin.lcs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dollar_price' => 'required|numeric|min:0',
            'usd_rate' => 'required|numeric|min:0',
            'expenses.*.expense_name' => 'required|string|max:255',
            'expenses.*.amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $dollar_price = $request->dollar_price ?? 0;
                $usd_rate = $request->usd_rate ?? 0;
                
                $lc_amount_bdt = $dollar_price * $usd_rate;
                $total_expense = 0;
                
                if ($request->has('expenses')) {
                    foreach ($request->expenses as $expense) {
                        $total_expense += $expense['amount'] ?? 0;
                    }
                }
                
                $final_cost = $lc_amount_bdt + $total_expense;
                $per_dollar_cost = $dollar_price > 0 ? ($total_expense / $dollar_price) : 0;

                $lc = Lc::create([
                    'name' => $request->name,
                    'dollar_price' => $dollar_price,
                    'usd_rate' => $usd_rate,
                    'lc_amount_bdt' => $lc_amount_bdt,
                    'total_expense' => $total_expense,
                    'final_cost' => $final_cost,
                    'per_dollar_cost' => $per_dollar_cost,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                if ($request->has('expenses')) {
                    foreach ($request->expenses as $expense) {
                        LcExpense::create([
                            'lc_id' => $lc->id,
                            'expense_name' => $expense['expense_name'],
                            'amount' => $expense['amount'] ?? 0,
                        ]);
                    }
                }
            });

            flash(__('custom.lc_created_successfully'))->success();
            return redirect()->route('admin.lcs.index');
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    public function show(Lc $lc)
    {
        set_page_meta(__('custom.lc_details'));
        $lc->load('expenses', 'creator', 'updater');
        return view('admin.lcs.show', compact('lc'));
    }

    public function edit(Lc $lc)
    {
        set_page_meta(__('custom.edit_lc'));
        $lc->load('expenses');
        return view('admin.lcs.edit', compact('lc'));
    }

    public function update(Request $request, Lc $lc)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'dollar_price' => 'required|numeric|min:0',
            'usd_rate' => 'required|numeric|min:0',
            'expenses.*.expense_name' => 'required|string|max:255',
            'expenses.*.amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $lc) {
                $dollar_price = $request->dollar_price ?? 0;
                $usd_rate = $request->usd_rate ?? 0;
                
                $lc_amount_bdt = $dollar_price * $usd_rate;
                $total_expense = 0;
                
                if ($request->has('expenses')) {
                    foreach ($request->expenses as $expense) {
                        $total_expense += $expense['amount'] ?? 0;
                    }
                }
                
                $final_cost = $lc_amount_bdt + $total_expense;
                $per_dollar_cost = $dollar_price > 0 ? ($total_expense / $dollar_price) : 0;

                $lc->update([
                    'name' => $request->name,
                    'dollar_price' => $dollar_price,
                    'usd_rate' => $usd_rate,
                    'lc_amount_bdt' => $lc_amount_bdt,
                    'total_expense' => $total_expense,
                    'final_cost' => $final_cost,
                    'per_dollar_cost' => $per_dollar_cost,
                    'updated_by' => Auth::id(),
                ]);

                // Update expenses: delete existing and recreate for simplicity
                $lc->expenses()->delete();

                if ($request->has('expenses')) {
                    foreach ($request->expenses as $expense) {
                        LcExpense::create([
                            'lc_id' => $lc->id,
                            'expense_name' => $expense['expense_name'],
                            'amount' => $expense['amount'] ?? 0,
                        ]);
                    }
                }
            });

            flash(__('custom.lc_updated_successfully'))->success();
            return redirect()->route('admin.lcs.index');
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    public function destroy(Lc $lc)
    {
        try {
            $lc->delete();
            flash(__('custom.lc_deleted_successfully'))->success();
            return redirect()->route('admin.lcs.index');
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->route('admin.lcs.index');
        }
    }
}
