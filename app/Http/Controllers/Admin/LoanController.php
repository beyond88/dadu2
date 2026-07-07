<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Loan List|Loan Create|Loan Edit|Loan Delete']);
    }

    public function index(Request $request)
    {
        set_page_meta(__('custom.loan_management'));

        $query = Loan::with('creator')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('loan_type')) {
            $query->where('loan_type', $request->loan_type);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('borrower_name', 'like', '%' . $request->search . '%')
                  ->orWhere('loan_no', 'like', '%' . $request->search . '%')
                  ->orWhere('borrower_phone', 'like', '%' . $request->search . '%');
            });
        }

        $loans = $query->paginate(20)->appends($request->all());

        $totalTaken     = Loan::where('loan_type', 'taken')->sum('total_amount');
        $totalRemaining = Loan::sum('remaining_amount');

        $loanStatuses = Loan::getStatuses();

        return view('admin.loans.index', compact(
            'loans', 'totalTaken', 'totalRemaining', 'loanStatuses'
        ));
    }

    public function create()
    {
        set_page_meta(__('custom.add_loan'));
        $loanTypes = Loan::getTypes();
        $accounts = Account::active()->get();
        return view('admin.loans.create', compact('loanTypes', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'borrower_name'   => 'required|string|max:255',
            'borrower_phone'  => 'nullable|string|max:30',
            'borrower_address'=> 'nullable|string|max:500',
            'account_id'      => 'required|exists:accounts,id',
            'opening_balance' => 'required|numeric|min:0.01',
            'loan_date'       => 'required|date',
            'due_date'        => 'nullable|date|after_or_equal:loan_date',
            'note'            => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();
            $loan = Loan::create([
                'loan_no'          => Loan::generateLoanNo(),
                'borrower_name'    => $request->borrower_name,
                'borrower_phone'   => $request->borrower_phone,
                'borrower_address' => $request->borrower_address,
                'loan_type'        => $request->loan_type ?? 'taken',
                'opening_balance'  => $request->opening_balance,
                'total_amount'     => $request->opening_balance,
                'paid_amount'      => 0,
                'remaining_amount' => $request->opening_balance,
                'loan_date'        => $request->loan_date,
                'due_date'         => $request->due_date,
                'status'           => Loan::STATUS_ACTIVE,
                'note'             => $request->note,
                'created_by'       => Auth::id(),
                'updated_by'       => Auth::id(),
            ]);

            // Add balance to account (since loan is 'taken', we receive money)
            $account = Account::findOrFail($request->account_id);
            $account->addBalance(
                $request->opening_balance,
                'Loan opening balance from ' . $request->borrower_name,
                $loan->id,
                Loan::class
            );

            DB::commit();
            flash(__('custom.loan_created_successfully'))->success();
            return redirect()->route('admin.loans.show', $loan->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        flash(__('custom.loan_created_successfully'))->success();
        return redirect()->route('admin.loans.show', $loan->id);
    }

    public function show(Loan $loan)
    {
        set_page_meta(__('custom.loan_details'));
        $loan->load(['payments.account', 'payments.creator', 'creator']);
        $accounts = Account::active()->get();
        return view('admin.loans.show', compact('loan', 'accounts'));
    }

    public function edit(Loan $loan)
    {
        set_page_meta(__('custom.edit_loan'));
        $loanTypes = Loan::getTypes();
        return view('admin.loans.edit', compact('loan', 'loanTypes'));
    }

    public function update(Request $request, Loan $loan)
    {
        $request->validate([
            'borrower_name'   => 'required|string|max:255',
            'borrower_phone'  => 'nullable|string|max:30',
            'borrower_address'=> 'nullable|string|max:500',
            // 'loan_type'       => 'required|in:given,taken',
            'loan_date'       => 'required|date',
            'due_date'        => 'nullable|date|after_or_equal:loan_date',
            'note'            => 'nullable|string|max:1000',
        ]);

        $loan->update([
            'borrower_name'    => $request->borrower_name,
            'borrower_phone'   => $request->borrower_phone,
            'borrower_address' => $request->borrower_address,
            'loan_type'        => $request->loan_type ?? 'taken',
            'loan_date'        => $request->loan_date,
            'due_date'         => $request->due_date,
            'note'             => $request->note,
            'updated_by'       => Auth::id(),
        ]);

        flash(__('custom.loan_updated_successfully'))->success();
        return redirect()->route('admin.loans.show', $loan->id);
    }

    public function destroy(Loan $loan)
    {
        if ($loan->payments()->count() > 0) {
            flash(__('custom.cannot_delete_loan_with_payments'))->error();
            return redirect()->route('admin.loans.index');
        }

        $loan->delete();
        flash(__('custom.loan_deleted_successfully'))->success();
        return redirect()->route('admin.loans.index');
    }

    /**
     * Store a loan payment (repayment from an account)
     */
    public function storePayment(Request $request, Loan $loan)
    {
        $request->validate([
            'account_id'     => 'required|exists:accounts,id',
            'amount'         => 'required|numeric|min:0.01|max:' . $loan->remaining_amount,
            'payment_date'   => 'required|date',
            'payment_method' => 'nullable|string|max:100',
            'reference_no'   => 'nullable|string|max:255',
            'note'           => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $loan) {
                $account = Account::findOrFail($request->account_id);

                // Deduct/add from account depending on loan type
                // If loan is 'given' (we gave money), receiving repayment = ADD to account
                // If loan is 'taken' (we received money), repaying = REDUCE from account
                if ($loan->loan_type === Loan::TYPE_GIVEN) {
                    $account->addBalance(
                        $request->amount,
                        'Loan repayment from ' . $loan->borrower_name . ' [' . $loan->loan_no . ']',
                        $loan->id,
                        'loan'
                    );
                } else {
                    $account->reduceBalance(
                        $request->amount,
                        'Loan repayment to ' . $loan->borrower_name . ' [' . $loan->loan_no . ']',
                        $loan->id,
                        'loan'
                    );
                }

                // Record the payment
                LoanPayment::create([
                    'loan_id'        => $loan->id,
                    'account_id'     => $request->account_id,
                    'amount'         => $request->amount,
                    'payment_date'   => $request->payment_date,
                    'payment_method' => $request->payment_method ?? 'cash',
                    'reference_no'   => $request->reference_no,
                    'note'           => $request->note,
                    'created_by'     => Auth::id(),
                ]);

                // Recalculate loan balances
                $loan->recalculate();
            });

            flash(__('custom.loan_payment_added_successfully'))->success();
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
        }

        return redirect()->route('admin.loans.show', $loan->id);
    }

    /**
     * Delete a loan payment
     */
    public function destroyPayment(Request $request, Loan $loan, LoanPayment $payment)
    {
        try {
            DB::transaction(function () use ($loan, $payment) {
                $account = Account::findOrFail($payment->account_id);

                // Reverse the account transaction
                if ($loan->loan_type === Loan::TYPE_GIVEN) {
                    $account->reduceBalance(
                        $payment->amount,
                        'Loan payment reversal [' . $loan->loan_no . ']',
                        $loan->id,
                        'loan'
                    );
                } else {
                    $account->addBalance(
                        $payment->amount,
                        'Loan payment reversal [' . $loan->loan_no . ']',
                        $loan->id,
                        'loan'
                    );
                }

                $payment->delete();
                $loan->recalculate();
            });

            flash(__('custom.loan_payment_deleted_successfully'))->success();
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
        }

        return redirect()->route('admin.loans.show', $loan->id);
    }

    /**
     * Transaction history for all loans
     */
    public function transactionHistory(Request $request)
    {
        set_page_meta(__('custom.loan_transaction_history'));

        $query = LoanPayment::with(['loan', 'account', 'creator'])->latest();

        if ($request->filled('loan_id')) {
            $query->where('loan_id', $request->loan_id);
        }
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('payment_date', [$request->from_date, $request->to_date]);
        }

        $payments  = $query->paginate(25)->appends($request->all());
        $loans     = Loan::orderBy('loan_no')->get();
        $accounts  = Account::active()->get();

        return view('admin.loans.transactions', compact('payments', 'loans', 'accounts'));
    }
}
