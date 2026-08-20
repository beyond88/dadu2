<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Account;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InvoicePaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Make Payment Invoice'])->only(['createCustomerPayment', 'storeCustomerPayment']);
        $this->middleware(['permission:View Payment Invoice'])->only(['customerPaymentHistory']);
    }

    /**
     * Show the make-payment form for a customer (Bulk payment across invoices)
     */
    public function createCustomerPayment(Customer $customer)
    {
        $totalInvoiced = $customer->totalInvoiced();
        $totalPaid     = $customer->totalPaid();
        $due           = $customer->totalDue();
        $accounts      = Account::active()->get();

        set_page_meta(__t('make_payment') . ' — ' . $customer->full_name);

        return view('admin.invoices.payment.customer-payment', compact('customer', 'totalPaid', 'due', 'accounts', 'totalInvoiced'));
    }

    /**
     * Store payment from a customer (FIFO across unpaid invoices)
     */
    public function storeCustomerPayment(Request $request, Customer $customer)
    {
        
        $request->validate([
            'date'         => 'required|date',
            'account_id'   => 'required|exists:accounts,id',
            'amount'       => 'required|numeric|min:0.01',
            'notes'        => 'nullable|string|max:500',
        ]);

        // The account chosen is the payment method, so the label stored on the
        // payment is taken from it. Asking for both let them contradict each other.
        $account     = Account::findOrFail($request->account_id);
        $paymentType = $this->paymentTypeForAccount($account);

        // Calculate total due for validation
        $totalDue = $customer->totalDue();

        if ($request->amount > $totalDue) {
            flash(__t('payment_amount_exceeds_due'))->error();
            return back()->withInput();
        }

        try {
            \Illuminate\Support\Facades\Log::info("=== START CUSTOMER PAYMENT ===");
            \Illuminate\Support\Facades\Log::info("Processing payment for Customer: {$customer->full_name} (ID: {$customer->id}), Total Payment Amount: {$request->amount}");
            
            DB::transaction(function () use ($request, $customer, $account, $paymentType) {
                $paymentAmount = $request->amount;
                // Get unpaid or partially paid invoices for this customer (FIFO by date)
                $unpaidInvoices = Invoice::where('customer_id', $customer->id)
                    ->orderBy('date', 'asc')
                    ->get();

                foreach ($unpaidInvoices as $invoice) {
                    if ($paymentAmount <= 0) break;

                    $invoicePaid = InvoicePayment::where('invoice_id', $invoice->id)->sum('amount');
                    $invoiceDue  = $invoice->total - $invoicePaid;
                    
                    if ($invoiceDue <= 0) continue;

                    $allocation = min($paymentAmount, $invoiceDue);

                    \Illuminate\Support\Facades\Log::info("Allocating {$allocation} to Invoice ID: {$invoice->id} (Invoice Due was: {$invoiceDue}).");

                    // Create Payment Record
                    InvoicePayment::create([
                        'invoice_id'   => $invoice->id,
                        'date'         => $request->date,
                        'payment_type' => $paymentType,
                        'amount'       => $allocation,
                        'notes'        => $request->notes . " (Bulk Customer Payment)",
                        'created_by'   => auth()->id(),
                        'bank_info'    => ['bank_name' => $request->account_id]
                    ]);

                    // Update invoice total_paid and status
                    $invoice->setAttribute('total_paid', (float)$invoice->getRawOriginal('total_paid') + $allocation);
                    if ($invoice->total_paid >= $invoice->total) {
                        $invoice->status = Invoice::STATUS_PAID;
                    } else {
                        $invoice->status = Invoice::STATUS_PARTIALLY_PAID;
                    }
                    $invoice->save();

                    // Book the money per invoice, matching the payment row that was
                    // just created. A single lump transaction could not be traced
                    // back to one payment, so deleting a row reversed the wrong one.
                    $account->recordInvoicePayment(
                        (float) $allocation,
                        $invoice->id,
                        "Customer Bulk Payment: " . $customer->full_name
                    );

                    $paymentAmount -= $allocation;
                    \Illuminate\Support\Facades\Log::info("Remaining Payment Amount after Invoice {$invoice->id} allocation: {$paymentAmount}");
                }

                if ($paymentAmount > 0) {
                    \Illuminate\Support\Facades\Log::info("Crediting remaining {$paymentAmount} to Opening Balance. Previous Opening Balance: {$customer->opening_balance}");

                    // Money received beyond the invoices it could be allocated to
                    // is credit the customer now holds, so it is added, not taken.
                    $customer->opening_balance += $paymentAmount;
                    $customer->save();

                    \Illuminate\Support\Facades\Log::info("New Opening Balance for Customer ID: {$customer->id} is now: {$customer->opening_balance}");

                    // Create Payment Record for Opening Balance
                    InvoicePayment::create([
                        'customer_id'  => $customer->id,
                        'invoice_id'   => null,
                        'date'         => $request->date,
                        'payment_type' => $paymentType,
                        'amount'       => $paymentAmount,
                        'notes'        => $request->notes ? $request->notes . " (Opening Balance Payment)" : "Opening Balance Payment",
                        'created_by'   => auth()->id(),
                        'bank_info'    => ['bank_name' => $request->account_id]
                    ]);

                    // The surplus is real money too — booked with no invoice
                    // reference, which is how its payment row is matched on delete.
                    $account->recordInvoicePayment(
                        (float) $paymentAmount,
                        null,
                        "Customer Bulk Payment: " . $customer->full_name . " (credit)"
                    );
                }

                \Illuminate\Support\Facades\Log::info("Payment successfully recorded to Account ID: {$request->account_id}. === END CUSTOMER PAYMENT ===");
            });

            flash(__t('payment_added_successful'))->success();
            return redirect()->route('admin.customers.index');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to process customer payment for Customer ID: {$customer->id}. Error: " . $e->getMessage());
            flash($e->getMessage())->error();
            return back()->withInput();
        }
    }

    /**
     * The payment method label for an account. The account itself is the method,
     * so this is derived rather than asked for — the two can never disagree.
     */
    private function paymentTypeForAccount(Account $account): string
    {
        return match ($account->type) {
            Account::TYPE_CASH           => 'cash',
            Account::TYPE_MOBILE_BANKING => 'mobile',
            default                      => 'bank',
        };
    }

    /**
     * Show payment history for a customer
     */
    public function customerPaymentHistory(Customer $customer)
    {
        $invoiceIds = Invoice::where('customer_id', $customer->id)->pluck('id');
        $payments    = InvoicePayment::where(function($q) use ($invoiceIds, $customer) {
                            $q->whereIn('invoice_id', $invoiceIds)
                              ->orWhere('customer_id', $customer->id);
                        })
                        ->with(['invoice'])
                        ->orderBy('id', 'desc')
                        ->get();
        
        // The listing shows every payment received (including credit top-ups that
        // belong to no invoice), while the due only counts invoice-linked ones.
        $totalInvoiced = $customer->totalInvoiced();
        $totalPaid     = $payments->sum('amount');
        $due           = $customer->totalDue();

        set_page_meta(__t('payment_history') . ' — ' . $customer->full_name);

        return view('admin.invoices.payment.customer-history', compact('customer', 'payments', 'totalPaid', 'due', 'totalInvoiced'));
    }
}
