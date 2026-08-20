<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lc;
use App\Models\LcExpense;
use App\Models\LcItem;
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
        $this->dropBlankRows($request);
        $request->validate($this->rules());

        try {
            DB::transaction(function () use ($request) {
                $items    = $request->input('items', []);
                $expenses = $request->input('expenses', []);
                $totals   = $this->calculateTotals($items, $expenses, $request->usd_rate ?? 0);

                $lc = Lc::create($totals + [
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                $this->syncItemsAndExpenses($lc, $items, $expenses);
            });

            flash(__('custom.lc_created_successfully'))->success();
            return redirect()->route('admin.lcs.index');
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    /**
     * Shared validation: an LC is one USD rate over many named LC amounts.
     */
    private function rules(): array
    {
        return [
            'usd_rate' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.dollar_price' => 'required|numeric|min:0',
            'expenses.*.expense_name' => 'required|string|max:255',
            'expenses.*.amount' => 'required|numeric|min:0',
        ];
    }

    /**
     * The forms ship preset LC/expense rows, so drop the ones left untouched
     * before validating — only named rows are real entries.
     */
    private function dropBlankRows(Request $request): void
    {
        $items = array_values(array_filter($request->input('items', []), function ($item) {
            return filled($item['name'] ?? null) || filled($item['dollar_price'] ?? null);
        }));

        // Expense names come prefilled, so an amount is what marks a row as used.
        $expenses = array_values(array_filter($request->input('expenses', []), function ($expense) {
            return filled($expense['amount'] ?? null);
        }));

        $request->merge(['items' => $items, 'expenses' => $expenses]);
    }

    /**
     * All LC rows share one USD rate, so the calculation runs on their total.
     */
    private function calculateTotals(array $items, array $expenses, $usd_rate): array
    {
        $dollar_price = 0;
        foreach ($items as $item) {
            $dollar_price += (float) ($item['dollar_price'] ?? 0);
        }

        $total_expense = 0;
        foreach ($expenses as $expense) {
            $total_expense += (float) ($expense['amount'] ?? 0);
        }

        $lc_amount_bdt = $dollar_price * $usd_rate;
        $final_cost    = $lc_amount_bdt + $total_expense;

        // Landed cost of one dollar: the USD rate plus the expense share it
        // carries, i.e. final_cost / dollar_price. The expense share alone is
        // not the cost of a dollar.
        $per_dollar_cost = $dollar_price > 0 ? ($final_cost / $dollar_price) : $usd_rate;

        return [
            // The list/report columns still show a single name — join the rows.
            'name' => \Illuminate\Support\Str::limit(implode(', ', array_column($items, 'name')), 250),
            'dollar_price' => $dollar_price,
            'usd_rate' => $usd_rate,
            'lc_amount_bdt' => $lc_amount_bdt,
            'total_expense' => $total_expense,
            'final_cost' => $final_cost,
            'per_dollar_cost' => $per_dollar_cost,
        ];
    }

    private function syncItemsAndExpenses(Lc $lc, array $items, array $expenses): void
    {
        $lc->items()->delete();
        $lc->expenses()->delete();

        foreach ($items as $item) {
            LcItem::create([
                'lc_id' => $lc->id,
                'name' => $item['name'],
                'dollar_price' => $item['dollar_price'] ?? 0,
            ]);
        }

        foreach ($expenses as $expense) {
            LcExpense::create([
                'lc_id' => $lc->id,
                'expense_name' => $expense['expense_name'],
                'amount' => $expense['amount'] ?? 0,
            ]);
        }
    }

    public function show(Lc $lc)
    {
        set_page_meta(__('custom.lc_details'));
        $lc->load('items', 'expenses', 'creator', 'updater');
        return view('admin.lcs.show', compact('lc'));
    }

    public function edit(Lc $lc)
    {
        set_page_meta(__('custom.edit_lc'));
        $lc->load('items', 'expenses');
        return view('admin.lcs.edit', compact('lc'));
    }

    public function update(Request $request, Lc $lc)
    {
        $this->dropBlankRows($request);
        $request->validate($this->rules());

        try {
            DB::transaction(function () use ($request, $lc) {
                $items    = $request->input('items', []);
                $expenses = $request->input('expenses', []);
                $totals   = $this->calculateTotals($items, $expenses, $request->usd_rate ?? 0);

                $lc->update($totals + ['updated_by' => Auth::id()]);

                // Rows are deleted and recreated for simplicity
                $this->syncItemsAndExpenses($lc, $items, $expenses);
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
