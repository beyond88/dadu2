<?php

namespace App\Http\Controllers\Api\v100\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Capital;
use App\Models\CapitalPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CapitalsController extends Controller
{
    public function index(Request $request)
    {
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

        $capitals = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $capitals,
            'summary' => [
                'total_capital' => Capital::sum('total_amount'),
                'total_remaining' => Capital::sum('remaining_amount'),
                'total_paid' => Capital::sum('paid_amount'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'investor_name'   => 'required|string|max:255',
            'investor_phone'  => 'nullable|string|max:30',
            'investor_address'=> 'nullable|string|max:500',
            'account_id'      => 'required|exists:accounts,id',
            'total_amount'    => 'required|numeric|min:0.01',
            'capital_date'    => 'required|date',
            'due_date'        => 'nullable|date|after_or_equal:capital_date',
            'note'            => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

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
                'Capital opening balance from ' . $request->investor_name . ' [' . $capital->capital_no . ']',
                $capital->id,
                Capital::class
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $capital,
                'message' => __('custom.capital_created_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show($id)
    {
        $capital = Capital::with(['payments.account', 'payments.creator', 'creator'])->find($id);

        if (!$capital) {
            return response()->json([
                'success' => false,
                'message' => 'Capital not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $capital,
        ]);
    }

    public function update(Request $request, $id)
    {
        $capital = Capital::find($id);

        if (!$capital) {
            return response()->json([
                'success' => false,
                'message' => 'Capital not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'investor_name'   => 'required|string|max:255',
            'investor_phone'  => 'nullable|string|max:30',
            'investor_address'=> 'nullable|string|max:500',
            'capital_date'    => 'required|date',
            'due_date'        => 'nullable|date|after_or_equal:capital_date',
            'note'            => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $capital->update([
                'investor_name'    => $request->investor_name,
                'investor_phone'   => $request->investor_phone,
                'investor_address' => $request->investor_address,
                'capital_date'     => $request->capital_date,
                'due_date'         => $request->due_date,
                'note'             => $request->note,
                'updated_by'       => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $capital,
                'message' => __('custom.capital_updated_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy($id)
    {
        $capital = Capital::find($id);

        if (!$capital) {
            return response()->json([
                'success' => false,
                'message' => 'Capital not found',
            ], 404);
        }

        if ($capital->payments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => __('custom.cannot_delete_capital_with_payments'),
            ], 400);
        }

        $capital->delete();

        return response()->json([
            'success' => true,
            'message' => __('custom.capital_deleted_successfully'),
        ]);
    }

    public function storePayment(Request $request, $id)
    {
        $capital = Capital::find($id);

        if (!$capital) {
            return response()->json([
                'success' => false,
                'message' => 'Capital not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'account_id'     => 'required|exists:accounts,id',
            'amount'         => 'required|numeric|min:0.01|max:' . $capital->remaining_amount,
            'payment_date'   => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'reference_no'   => 'nullable|string|max:100',
            'note'           => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
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
            $account->reduceBalance(
                $request->amount,
                'Capital payment to ' . $capital->investor_name . ' - ' . $capital->capital_no,
                $payment->id,
                CapitalPayment::class
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('custom.capital_payment_successful'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroyPayment($capital_id, $payment_id)
    {
        $capital = Capital::find($capital_id);
        $payment = CapitalPayment::find($payment_id);

        if (!$capital || !$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Capital or Payment not found',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Restore account balance
            $account = Account::findOrFail($payment->account_id);
            $account->addBalance(
                $payment->amount,
                'Capital payment reversal [' . $capital->capital_no . ']',
                $capital->id,
                Capital::class
            );

            // Delete payment
            $payment->delete();

            // Recalculate capital
            $capital->recalculate();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('custom.payment_deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function addAmount(Request $request, $id)
    {
        $capital = Capital::find($id);

        if (!$capital) {
            return response()->json([
                'success' => false,
                'message' => 'Capital not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'account_id'   => 'required|exists:accounts,id',
            'amount'       => 'required|numeric|min:0.01',
            'date'         => 'required|date',
            'note'         => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

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

            return response()->json([
                'success' => true,
                'message' => __('custom.capital_amount_added_successfully'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function transactionHistory(Request $request)
    {
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

        $transactions = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }
}
