<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\DataTables\AccountDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Account List|Account Create|Account Edit|Account Delete']);
    }

    public function index(AccountDataTable $dataTable)
    {
        set_page_meta(__('custom.accounts'));
        return $dataTable->render('admin.accounts.index');
    }

    public function create()
    {
        set_page_meta(__('custom.add_account'));
        $accountTypes = Account::getTypes();
        return view('admin.accounts.create', compact('accountTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,mobile_banking',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'opening_balance' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Singleton Cash account: only ONE active Cash account is allowed app-wide
        // (there is no DB unique constraint, so this guard prevents a second one).
        if ($request->type === Account::TYPE_CASH
            && Account::where('type', Account::TYPE_CASH)->where('is_active', true)->exists()) {
            flash('Only one active Cash account is allowed — a Cash account already exists.')->error();
            return redirect()->back()->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
                $account = Account::create([
                    'name' => $request->name,
                    'code' => $request->code,
                    'type' => $request->type,
                    'account_number' => $request->account_number,
                    'bank_name' => $request->bank_name,
                    'branch_name' => $request->branch_name,
                    'current_balance' => 0,
                    'is_active' => $request->is_active ?? true,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                // Record opening balance as transaction if provided
                if ($request->filled('opening_balance') && $request->opening_balance > 0) {
                    $account->recordOpeningBalance($request->opening_balance);
                }
            });

            flash(__('custom.account_created_successfully'))->success();
            return redirect()->route('admin.accounts.index');
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    public function show(Account $account)
    {
        set_page_meta(__('custom.account_details'));
        $account->load(['creator', 'updater', 'transactions' => function ($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('admin.accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        set_page_meta(__('custom.edit_account'));
        $accountTypes = Account::getTypes();
        return view('admin.accounts.edit', compact('account', 'accountTypes'));
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'type' => 'required|in:cash,bank,mobile_banking',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        // Singleton Cash account: block turning this account into a second active Cash account.
        if ($request->type === Account::TYPE_CASH
            && ($request->is_active ?? true)
            && Account::where('type', Account::TYPE_CASH)->where('is_active', true)
                ->where('id', '!=', $account->id)->exists()) {
            flash('Only one active Cash account is allowed — a Cash account already exists.')->error();
            return redirect()->back()->withInput();
        }

        $account->update([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'is_active' => $request->is_active ?? true,
            'updated_by' => Auth::id(),
        ]);

        flash(__('custom.account_updated_successfully'))->success();
        return redirect()->route('admin.accounts.index');
    }

    public function destroy(Account $account)
    {
        // Check if account has transactions
        if ($account->transactions()->count() > 0) {
            flash(__('custom.cannot_delete_account_with_transactions'))->error();
            return redirect()->route('admin.accounts.index');
        }

        $account->delete();
        flash(__('custom.account_deleted_successfully'))->success();
        return redirect()->route('admin.accounts.index');
    }

    public function toggleStatus(Account $account)
    {
        $account->update([
            'is_active' => !$account->is_active,
            'updated_by' => Auth::id(),
        ]);

        $status = $account->is_active ? 'activated' : 'deactivated';
        flash(__('custom.account_status_updated_successfully', ['status' => $status]))->success();
        return redirect()->route('admin.accounts.index');
    }

    public function getBalance(Account $account)
    {
        return response()->json([
            'balance' => $account->current_balance,
            'formatted_balance' => number_format($account->current_balance, 2),
        ]);
    }

    public function getAccountsByType($type)
    {
        $accounts = Account::where('type', $type)
            ->active()
            ->get(['id', 'name', 'code', 'current_balance']);
        
        return response()->json($accounts);
    }
}
