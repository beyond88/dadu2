<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use App\DataTables\TransactionDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Transaction List|Transaction Create']);
    }

    public function index(TransactionDataTable $dataTable)
    {
        set_page_meta(__('custom.transactions'));
        return $dataTable->render('admin.transactions.index');
    }

    public function create()
    {
        set_page_meta(__('custom.add_transaction'));
        $accounts = Account::active()->get();
        $transactionTypes = Transaction::getTypes();
        $nextTransactionNo = (Transaction::max('id') ?? 0) + 1;

        return view('admin.transactions.create', compact('accounts', 'transactionTypes', 'nextTransactionNo'));
    }

    /**
     * Credit-type transactions increase the balance; debit-type decrease it.
     */
    private function isCreditType($type)
    {
        return in_array($type, [
            Transaction::TYPE_ADD,
            Transaction::TYPE_TRANSFER_IN,
            Transaction::TYPE_INVOICE_PAYMENT,
            Transaction::TYPE_DUE_COLLECTION,
            Transaction::TYPE_OPENING_BALANCE,
        ]);
    }

    private function transactionRow(Transaction $t)
    {
        $credit = $this->isCreditType($t->type);
        return [
            'id'     => $t->id,
            'accno'  => optional($t->account)->account_number,
            'note'   => $t->note,
            'date'   => $t->created_at ? $t->created_at->format('d/m/Y') : '',
            'debit'  => $credit ? '' : number_format($t->amount, 2),
            'credit' => $credit ? number_format($t->amount, 2) : '',
        ];
    }

    /**
     * Look up an active account by its Code (desktop "Account Code" flow).
     */
    public function accountByCode(Request $request)
    {
        $code = trim((string) $request->query('code'));

        if ($code === '') {
            return response()->json(['success' => false, 'message' => __('custom.account_not_found')], 404);
        }

        $account = Account::active()->where('code', $code)->first();

        if (!$account) {
            return response()->json(['success' => false, 'message' => __('custom.account_not_found')], 404);
        }

        $transactions = $account->transactions()
            ->with('account')
            ->latest()
            ->take(20)
            ->get()
            ->map(fn ($t) => $this->transactionRow($t));

        return response()->json([
            'success' => true,
            'account' => [
                'id'                        => $account->id,
                'account_number'            => $account->account_number,
                'bank_name'                 => $account->bank_name,
                'name'                      => $account->name,
                'current_balance'           => (float) $account->current_balance,
                'current_balance_formatted' => number_format($account->current_balance, 2),
            ],
            'transactions' => $transactions,
        ]);
    }

    /**
     * Instant debit/credit entry (desktop "press Enter to save") returning the new balance.
     */
    public function quickStore(Request $request)
    {
        $request->validate([
            'account_id'  => 'required|exists:accounts,id',
            'debit'       => 'nullable|numeric|min:0',
            'credit'      => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'date'        => 'nullable|date',
        ]);

        $debit  = (float) $request->input('debit', 0);
        $credit = (float) $request->input('credit', 0);

        if ($debit <= 0 && $credit <= 0) {
            return response()->json(['success' => false, 'message' => __('custom.enter_debit_or_credit')], 422);
        }
        if ($debit > 0 && $credit > 0) {
            return response()->json(['success' => false, 'message' => __('custom.enter_only_one_debit_credit')], 422);
        }

        try {
            $account = Account::findOrFail($request->account_id);

            if ($credit > 0) {
                $transaction = $account->addBalance($credit, $request->description);
            } else {
                $transaction = $account->reduceBalance($debit, $request->description);
            }

            // Honour a user-chosen transaction date (keeps the current time of day).
            if ($request->filled('date')) {
                $transaction->created_at = \Carbon\Carbon::parse($request->date)->setTimeFrom(now());
                $transaction->save();
            }

            $account->refresh();
            $transaction->setRelation('account', $account);

            return response()->json([
                'success'               => true,
                'message'               => __('custom.transaction_created_successfully'),
                'new_balance'           => (float) $account->current_balance,
                'new_balance_formatted' => number_format($account->current_balance, 2),
                'transaction'           => $this->transactionRow($transaction),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:add,reduce,transfer',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            $account = Account::findOrFail($request->account_id);

            if ($request->type == 'add') {
                $account->addBalance($request->amount, $request->note);
                flash(__('custom.balance_added_successfully'))->success();
            } elseif ($request->type == 'reduce') {
                $account->reduceBalance($request->amount, $request->note);
                flash(__('custom.balance_reduced_successfully'))->success();
            } elseif ($request->type == 'transfer') {
                $request->validate([
                    'to_account_id' => 'required|exists:accounts,id|different:account_id',
                ]);
                $toAccount = Account::findOrFail($request->to_account_id);
                $account->transferTo($toAccount, $request->amount, $request->note);
                flash(__('custom.balance_transferred_successfully'))->success();
            }

            return redirect()->route('admin.transactions.index');
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    public function show(Transaction $transaction)
    {
        set_page_meta(__('custom.transaction_details'));
        $transaction->load(['account', 'fromAccount', 'toAccount', 'creator']);
        
        return view('admin.transactions.show', compact('transaction'));
    }

    public function transferForm()
    {
        set_page_meta(__('custom.transfer_balance'));
        $accounts = Account::active()->get();
        
        return view('admin.transactions.transfer', compact('accounts'));
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id|different:to_account_id',
            'to_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            $fromAccount = Account::findOrFail($request->from_account_id);
            $toAccount = Account::findOrFail($request->to_account_id);

            $fromAccount->transferTo($toAccount, $request->amount, $request->note);

            flash(__('custom.balance_transferred_successfully'))->success();
            return redirect()->route('admin.transactions.index');
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return redirect()->back()->withInput();
        }
    }

    public function accountStatement(Request $request, Account $account)
    {
        set_page_meta(__('custom.account_statement'));
        
        $transactions = $account->transactions()
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
            ->paginate(25);

        $transactionTypes = Transaction::getTypes();

        return view('admin.transactions.statement', compact('account', 'transactions', 'transactionTypes'));
    }

    public function getAccountBalance(Account $account)
    {
        return response()->json([
            'balance' => $account->current_balance,
            'formatted_balance' => number_format($account->current_balance, 2),
        ]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:add,reduce,transfer_in,transfer_out,invoice_payment,due_collection,opening_balance',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'reference_type' => 'nullable|string',
        ]);

        try {
            $account = Account::findOrFail($request->account_id);

            switch ($request->type) {
                case 'add':
                case 'transfer_in':
                case 'invoice_payment':
                case 'due_collection':
                    $transaction = $account->addBalance(
                        $request->amount,
                        $request->note,
                        $request->reference_id,
                        $request->reference_type
                    );
                    break;

                case 'reduce':
                case 'transfer_out':
                    $transaction = $account->reduceBalance(
                        $request->amount,
                        $request->note,
                        $request->reference_id,
                        $request->reference_type
                    );
                    break;

                case 'opening_balance':
                    $transaction = $account->recordOpeningBalance($request->amount);
                    break;

                default:
                    throw new \Exception(__('custom.invalid_transaction_type'));
            }

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
                'message' => __('custom.transaction_created_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function destroy(Transaction $transaction)
    {
        try {
            DB::transaction(function () use ($transaction) {
                $account = $transaction->account;
                
                // Reverse the balance based on transaction type
                if (in_array($transaction->type, [Transaction::TYPE_ADD, Transaction::TYPE_INVOICE_PAYMENT, Transaction::TYPE_DUE_COLLECTION, Transaction::TYPE_TRANSFER_IN, Transaction::TYPE_OPENING_BALANCE])) {
                    $account->decrement('current_balance', $transaction->amount);
                } elseif (in_array($transaction->type, [Transaction::TYPE_REDUCE, Transaction::TYPE_TRANSFER_OUT])) {
                    $account->increment('current_balance', $transaction->amount);
                }
                
                // Handle transfer counterparts
                if ($transaction->type === Transaction::TYPE_TRANSFER_OUT) {
                    $matchingTransferIn = Transaction::where('type', Transaction::TYPE_TRANSFER_IN)
                        ->where('account_id', $transaction->to_account_id)
                        ->where('from_account_id', $account->id)
                        ->where('amount', $transaction->amount)
                        ->where('created_at', $transaction->created_at)
                        ->first();
                    if ($matchingTransferIn) {
                        $matchingTransferIn->account->decrement('current_balance', $matchingTransferIn->amount);
                        $matchingTransferIn->delete();
                    }
                } elseif ($transaction->type === Transaction::TYPE_TRANSFER_IN) {
                    $matchingTransferOut = Transaction::where('type', Transaction::TYPE_TRANSFER_OUT)
                        ->where('account_id', $transaction->from_account_id)
                        ->where('to_account_id', $account->id)
                        ->where('amount', $transaction->amount)
                        ->where('created_at', $transaction->created_at)
                        ->first();
                    if ($matchingTransferOut) {
                        $matchingTransferOut->account->increment('current_balance', $matchingTransferOut->amount);
                        $matchingTransferOut->delete();
                    }
                }

                $transaction->delete();
            });

            flash(__('custom.transaction_deleted_successfully'))->success();
            return back();
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return back();
        }
    }
}
