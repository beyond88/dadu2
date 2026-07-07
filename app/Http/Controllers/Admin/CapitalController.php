<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Capital;
use App\Models\CapitalPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CapitalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Capital List|Capital Create|Capital Edit|Capital Delete']);
    }

    public function index(Request $request)
    {
        set_page_meta(__('custom.capital_management'));

        $query = Capital::with('creator')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('investor_name', 'like', '%' . $request->search . '%')
                  ->orWhere('capital_no', 'like', '%' . $request->search . '%')
                  ->orWhere('investor_phone', 'like', '%' . $request->search . '%');
            });
        }

        $capitals = $query->paginate(20)->appends($request->all());

        $totalCapital     = Capital::sum('total_amount');
        $totalRemaining   = Capital::sum('remaining_amount');
        $totalPaid        = Capital::sum('paid_amount');

        $capitalStatuses = Capital::getStatuses();

        return view('admin.capitals.index', compact(
            'capitals', 'totalCapital', 'totalRemaining', 'totalPaid', 'capitalStatuses'
        ));
    }

    public function create()
    {
        set_page_meta(__('custom.add_capital'));
        $accounts = Account::active()->get();
        return view('admin.capitals.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'investor_name'   => 'required|string|max:255',
            'investor_phone'  => 'nullable|string|max:30',
            'investor_address'=> 'nullable|string|max:500',
            'account_id'      => 'required|exists:accounts,id',
            'total_amount'    => 'required|numeric|min:0.01',
            'capital_date'    => 'required|date',
            'due_date'        => 'nullable|date|after_or_equal:capital_date',
            'note'            => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();
            $capital = Capital::create([
                'capital_no'       => Capital::generateCapitalNo(),
                'investor_name'    => $request->investor_name,
                'investor_phone'   => $request->investor_phone,
                'investor_address' => $request->investor_address,
                'total_amount'     => $request->total_amount,
                'paid_amount'      => 0,
                'remaining_amount' => $request->total_amount,
                'capital_date'     => $request->capital_date,
                'due_date'         => $request->due_date,
                'status'           => Capital::STATUS_ACTIVE,
                'note'             => $request->note,
                'created_by'       => Auth::id(),
                'updated_by'       => Auth::id(),
            ]);

            // Add balance to account
            $account = Account::findOrFail($request->account_id);
            $account->addBalance(
                $request->total_amount,
                'Capital opening balance from ' . $request->investor_name,
                $capital->id,
                Capital::class
            );

            DB::commit();
            flash(__('custom.capital_created_successfully'))->success();
            return redirect()->route('admin.capitals.show', $capital->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        flash(__('custom.capital_created_successfully'))->success();
        return redirect()->route('admin.capitals.show', $capital->id);
    }

    public function show(Capital $capital)
    {
        set_page_meta(__('custom.capital_details'));
        $capital->load(['payments.account', 'payments.creator', 'creator']);
        $accounts = Account::active()->get();
        return view('admin.capitals.show', compact('capital', 'accounts'));
    }

    public function edit(Capital $capital)
    {
        set_page_meta(__('custom.edit_capital'));
        return view('admin.capitals.edit', compact('capital'));
    }

    public function update(Request $request, Capital $capital)
    {
        $request->validate([
            'investor_name'   => 'required|string|max:255',
            'investor_phone'  => 'nullable|string|max:30',
            'investor_address'=> 'nullable|string|max:500',
            'capital_date'    => 'required|date',
            'due_date'        => 'nullable|date|after_or_equal:capital_date',
            'note'            => 'nullable|string|max:1000',
        ]);

        // Prevent editing if capital has payments
        if ($capital->payments()->count() > 0 && $request->total_amount != $capital->total_amount) {
            flash(__('custom.cannot_edit_capital_with_payments'))->error();
            return redirect()->route('admin.capitals.edit', $capital->id);
        }

        $capital->update([
            'investor_name'    => $request->investor_name,
            'investor_phone'   => $request->investor_phone,
            'investor_address' => $request->investor_address,
            'capital_date'     => $request->capital_date,
            'due_date'         => $request->due_date,
            'note'             => $request->note,
            'updated_by'       => Auth::id(),
        ]);

        flash(__('custom.capital_updated_successfully'))->success();
        return redirect()->route('admin.capitals.show', $capital->id);
    }

    public function destroy(Capital $capital)
    {
        if ($capital->payments()->count() > 0) {
            flash(__('custom.cannot_delete_capital_with_payments'))->error();
            return redirect()->route('admin.capitals.index');
        }

        $capital->delete();
        flash(__('custom.capital_deleted_successfully'))->success();
        return redirect()->route('admin.capitals.index');
    }

    /**
     * Store a capital payment
     */
    public function storePayment(Request $request, Capital $capital)
    {
        $request->validate([
            'account_id'     => 'required|exists:accounts,id',
            'amount'         => 'required|numeric|min:0.01',
            'payment_date'   => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'reference_no' => 'nullable|string|max:100',
            'note'           => 'nullable|string|max:1000',
        ]);

        // Check if payment amount exceeds remaining
        if ($request->amount > $capital->remaining_amount) {
            flash(__('custom.payment_amount_exceeds_remaining'))->error();
            return redirect()->route('admin.capitals.show', $capital->id);
        }

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = CapitalPayment::create([
                'capital_id'     => $capital->id,
                'account_id'     => $request->account_id,
                'amount'         => $request->amount,
                'payment_date'   => $request->payment_date,
                'payment_method' => $request->payment_method ?? 'cash',
                'reference_no'   => $request->reference_no,
                'note'           => $request->note,
                'created_by'     => Auth::id(),
            ]);

            // Update capital amounts
            $capital->recalculate();

            // Create expense transaction from account
            $account = Account::findOrFail($request->account_id);
            Transaction::create([
                'account_id'      => $request->account_id,
                'type'            => 'expense',
                'amount'          => $request->amount,
                'date'            => $request->payment_date,
                'reference_no'    => $request->reference_no,
                'description'     => 'Capital payment to ' . $capital->investor_name . ' - ' . $capital->capital_no,
                'transactionable_type' => CapitalPayment::class,
                'transactionable_id'   => $payment->id,
                'created_by'      => Auth::id(),
            ]);

            // Deduct from account balance
            $account->current_balance -= $request->amount;
            $account->save();

            DB::commit();

            flash(__('custom.capital_payment_successful'))->success();
            return redirect()->route('admin.capitals.show', $capital->id);
        } catch (\Exception $e) {
            DB::rollBack();
            flash(__('custom.something_went_wrong'))->error();
            return redirect()->route('admin.capitals.show', $capital->id);
        }
    }

    /**
     * Delete a capital payment
     */
    public function destroyPayment(Capital $capital, CapitalPayment $payment)
    {
        DB::beginTransaction();
        try {
            // Restore account balance
            $account = Account::findOrFail($payment->account_id);
            $account->current_balance += $payment->amount;
            $account->save();

            // Delete related transaction
            Transaction::where('transactionable_type', CapitalPayment::class)
                ->where('transactionable_id', $payment->id)
                ->delete();

            // Delete payment
            $payment->delete();

            // Recalculate capital
            $capital->recalculate();

            DB::commit();

            flash(__('custom.payment_deleted_successfully'))->success();
            return redirect()->route('admin.capitals.show', $capital->id);
        } catch (\Exception $e) {
            DB::rollBack();
            flash(__('custom.something_went_wrong'))->error();
            return redirect()->route('admin.capitals.show', $capital->id);
        }
    }

    /**
     * Add amount to capital
     */
    public function addAmount(Request $request, Capital $capital)
    {
        $request->validate([
            'account_id'   => 'required|exists:accounts,id',
            'amount'       => 'required|numeric|min:0.01',
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Update capital amounts
            $capital->total_amount += $request->amount;
            $capital->recalculate();

            // Add balance to account
            $account = Account::findOrFail($request->account_id);
            $account->addBalance(
                $request->amount,
                'Capital addition from ' . $capital->investor_name . ' - ' . $capital->capital_no . ($request->note ? ' (' . $request->note . ')' : ''),
                $capital->id,
                Capital::class
            );

            DB::commit();

            flash(__('custom.capital_amount_added_successfully'))->success();
            return redirect()->route('admin.capitals.show', $capital->id);
        } catch (\Exception $e) {
            DB::rollBack();
            flash(__('custom.something_went_wrong'))->error();
            return redirect()->route('admin.capitals.show', $capital->id);
        }
    }

    /**
     * Capital payment transaction history
     */
    public function transactionHistory(Request $request)
    {
        set_page_meta(__('custom.capital_transactions'));

        $query = CapitalPayment::with(['capital', 'account', 'creator'])->latest();

        if ($request->filled('capital_id')) {
            $query->where('capital_id', $request->capital_id);
        }
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        $transactions = $query->paginate(20)->appends($request->all());
        $capitals = Capital::all();
        $accounts = Account::active()->get();

        return view('admin.capitals.transactions', compact('transactions', 'capitals', 'accounts'));
    }
}
