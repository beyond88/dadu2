<?php

namespace App\Http\Controllers\Api\v100\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountsController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::with(['creator', 'updater'])
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('code', 'like', '%' . $request->search . '%');
                });
            })
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $accounts,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'type' => 'required|in:cash,bank,mobile_banking',
            'opening_balance' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $account = DB::transaction(function () use ($request) {
                $account = Account::create([
                    'name' => $request->name,
                    'code' => $request->code,
                    'type' => $request->type,
                    'current_balance' => 0,
                    'is_active' => $request->is_active ?? true,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                if ($request->filled('opening_balance') && $request->opening_balance > 0) {
                    $account->recordOpeningBalance($request->opening_balance);
                }

                return $account;
            });

            return response()->json([
                'success' => true,
                'data' => $account,
                'message' => __('custom.account_created_successfully'),
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
        $account = Account::with(['creator', 'updater', 'transactions' => function ($query) {
            $query->latest()->limit(20);
        }])->find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $account,
        ]);
    }

    public function update(Request $request, $id)
    {
        $account = Account::find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'type' => 'required|in:cash,bank,mobile_banking',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $account->update([
                'name' => $request->name,
                'code' => $request->code,
                'type' => $request->type,
                'is_active' => $request->is_active ?? true,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $account,
                'message' => __('custom.account_updated_successfully'),
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
        $account = Account::find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found',
            ], 404);
        }

        if ($account->transactions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => __('custom.cannot_delete_account_with_transactions'),
            ], 400);
        }

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => __('custom.account_deleted_successfully'),
        ]);
    }

    public function toggleStatus($id)
    {
        $account = Account::find($id);

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found',
            ], 404);
        }

        $account->update([
            'is_active' => !$account->is_active,
            'updated_by' => Auth::id(),
        ]);

        $status = $account->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'data' => $account,
            'message' => __('custom.account_status_updated_successfully', ['status' => $status]),
        ]);
    }

    public function getAccountsByType($type)
    {
        $accounts = Account::where('type', $type)
            ->where('is_active', true)
            ->get(['id', 'name', 'code', 'current_balance']);
        
        return response()->json([
            'success' => true,
            'data' => $accounts,
        ]);
    }
}
