<?php

namespace App\Http\Controllers\Api\v100\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoansController extends Controller
{
    public function index(Request $request)
    {
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

        $loans = $query->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $loans,
            'summary' => [
                'total_taken' => Loan::where('loan_type', 'taken')->sum('total_amount'),
                'total_remaining' => Loan::sum('remaining_amount'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'borrower_name'   => 'required|string|max:255',
            'borrower_phone'  => 'nullable|string|max:30',
            'borrower_address'=> 'nullable|string|max:500',
            'account_id'      => 'required|exists:accounts,id',
            'opening_balance' => 'required|numeric|min:0.01',
            'loan_date'       => 'required|date',
            'due_date'        => 'nullable|date|after_or_equal:loan_date',
            'note'            => 'nullable|string|max:1000',
            'loan_type'       => 'nullable|in:given,taken',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

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

            // Adjust account balance based on loan type
            $account = Account::findOrFail($request->account_id);
            
            if ($loan->loan_type === Loan::TYPE_GIVEN) {
                // If we give a loan, our account balance decreases
                $account->reduceBalance(
                    $request->opening_balance,
                    'Loan given to ' . $request->borrower_name . ' [' . $loan->loan_no . ']',
                    $loan->id,
                    Loan::class
                );
            } else {
                // If we take a loan, our account balance increases
                $account->addBalance(
                    $request->opening_balance,
                    'Loan taken from ' . $request->borrower_name . ' [' . $loan->loan_no . ']',
                    $loan->id,
                    Loan::class
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $loan,
                'message' => __('custom.loan_created_successfully'),
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
        $loan = Loan::with(['payments.account', 'payments.creator', 'creator'])->find($id);

        if (!$loan) {
            return response()->json([
                'success' => false,
                'message' => 'Loan not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $loan,
        ]);
    }

    public function update(Request $request, $id)
    {
        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json([
                'success' => false,
                'message' => 'Loan not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'borrower_name'   => 'required|string|max:255',
            'borrower_phone'  => 'nullable|string|max:30',
            'borrower_address'=> 'nullable|string|max:500',
            'loan_date'       => 'required|date',
            'due_date'        => 'nullable|date|after_or_equal:loan_date',
            'note'            => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $loan->update([
                'borrower_name'    => $request->borrower_name,
                'borrower_phone'   => $request->borrower_phone,
                'borrower_address' => $request->borrower_address,
                'loan_date'        => $request->loan_date,
                'due_date'         => $request->due_date,
                'note'             => $request->note,
                'updated_by'       => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $loan,
                'message' => __('custom.loan_updated_successfully'),
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
        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json([
                'success' => false,
                'message' => 'Loan not found',
            ], 404);
        }

        if ($loan->payments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => __('custom.cannot_delete_loan_with_payments'),
            ], 400);
        }

        $loan->delete();

        return response()->json([
            'success' => true,
            'message' => __('custom.loan_deleted_successfully'),
        ]);
    }

    public function storePayment(Request $request, $id)
    {
        $loan = Loan::find($id);

        if (!$loan) {
            return response()->json([
                'success' => false,
                'message' => 'Loan not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'account_id'     => 'required|exists:accounts,id',
            'amount'         => 'required|numeric|min:0.01|max:' . $loan->remaining_amount,
            'payment_date'   => 'required|date',
            'payment_method' => 'nullable|string|max:100',
            'reference_no'   => 'nullable|string|max:255',
            'note'           => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $loan) {
                $payment = LoanPayment::create([
                    'loan_id'        => $loan->id,
                    'account_id'     => $request->account_id,
                    'amount'         => $request->amount,
                    'payment_date'   => $request->payment_date,
                    'payment_method' => $request->payment_method ?? 'cash',
                    'reference_no'   => $request->reference_no,
                    'note'           => $request->note,
                    'created_by'     => Auth::id(),
                ]);

                $account = Account::findOrFail($request->account_id);

                if ($loan->loan_type === Loan::TYPE_GIVEN) {
                    $account->addBalance(
                        $request->amount,
                        'Loan repayment from ' . $loan->borrower_name . ' [' . $loan->loan_no . ']',
                        $payment->id,
                        LoanPayment::class
                    );
                } else {
                    $account->reduceBalance(
                        $request->amount,
                        'Loan repayment to ' . $loan->borrower_name . ' [' . $loan->loan_no . ']',
                        $payment->id,
                        LoanPayment::class
                    );
                }

                $loan->recalculate();
            });

            return response()->json([
                'success' => true,
                'message' => __('custom.loan_payment_added_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroyPayment($loan_id, $payment_id)
    {
        $loan = Loan::find($loan_id);
        $payment = LoanPayment::find($payment_id);

        if (!$loan || !$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Loan or Payment not found',
            ], 404);
        }

        try {
            DB::transaction(function () use ($loan, $payment) {
                $account = Account::findOrFail($payment->account_id);

                if ($loan->loan_type === Loan::TYPE_GIVEN) {
                    $account->reduceBalance(
                        $payment->amount,
                        'Loan payment reversal [' . $loan->loan_no . ']',
                        $loan->id,
                        Loan::class
                    );
                } else {
                    $account->addBalance(
                        $payment->amount,
                        'Loan payment reversal [' . $loan->loan_no . ']',
                        $loan->id,
                        Loan::class
                    );
                }

                $payment->delete();
                $loan->recalculate();
            });

            return response()->json([
                'success' => true,
                'message' => __('custom.loan_payment_deleted_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function transactionHistory(Request $request)
    {
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

        $payments = $query->paginate($request->per_page ?? 25);

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }
}
