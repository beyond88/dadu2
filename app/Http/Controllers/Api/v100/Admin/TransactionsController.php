<?php

namespace App\Http\Controllers\Api\v100\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with(['account', 'fromAccount', 'toAccount', 'creator'])
            ->when($request->account_id, function ($query) use ($request) {
                $query->where('account_id', $request->account_id);
            })
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->from_date && $request->to_date, function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    $request->from_date . ' 00:00:00',
                    $request->to_date . ' 23:59:59'
                ]);
            })
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $transactions,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:add,reduce,transfer',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:1000',
            'to_account_id' => 'required_if:type,transfer|exists:accounts,id|different:account_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $transaction = DB::transaction(function () use ($request) {
                $account = Account::findOrFail($request->account_id);
                $trans = null;

                if ($request->type == 'add') {
                    $trans = $account->addBalance($request->amount, $request->note);
                } elseif ($request->type == 'reduce') {
                    $trans = $account->reduceBalance($request->amount, $request->note);
                } elseif ($request->type == 'transfer') {
                    $toAccount = Account::findOrFail($request->to_account_id);
                    $trans = $account->transferTo($toAccount, $request->amount, $request->note);
                }

                return $trans;
            });

            return response()->json([
                'success' => true,
                'data' => $transaction,
                'message' => __('custom.transaction_created_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show($id)
    {
        $transaction = Transaction::with(['account', 'fromAccount', 'toAccount', 'creator'])->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaction,
        ]);
    }

    public function getTransactionTypes()
    {
        return response()->json([
            'success' => true,
            'data' => Transaction::getTypes(),
        ]);
    }

    public function getAccountStatement(Request $request, $account_id)
    {
        $account = Account::find($account_id);
        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found',
            ], 404);
        }

        $transactions = Transaction::where(function($query) use ($account_id) {
                $query->where('account_id', $account_id)
                      ->orWhere('from_account_id', $account_id)
                      ->orWhere('to_account_id', $account_id);
            })
            ->with(['fromAccount', 'toAccount', 'creator'])
            ->when($request->from_date && $request->to_date, function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    $request->from_date . ' 00:00:00',
                    $request->to_date . ' 23:59:59'
                ]);
            })
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->latest()
            ->paginate($request->per_page ?? 25);

        return response()->json([
            'success' => true,
            'account' => $account,
            'data' => $transactions,
        ]);
    }
}
