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
        // Get all invoices for this customer
        $invoices = Invoice::where('customer_id', $customer->id)->get();
        
        $totalInvoiced = $invoices->sum('total');
        $totalPaid     = InvoicePayment::whereIn('invoice_id', $invoices->pluck('id'))->sum('amount');
        $due           = ($totalInvoiced + ($customer->opening_balance ?? 0)) - $totalPaid;
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
            'payment_type' => 'required|string|max:100',
            'account_id'   => 'required|exists:accounts,id',
            'amount'       => 'required|numeric|min:0.01',
            'notes'        => 'nullable|string|max:500',
        ]);

        // Calculate total due for validation
        $invoices = Invoice::where('customer_id', $customer->id)->get();
        $totalInvoiced = $invoices->sum('total');
        $totalPaid      = InvoicePayment::whereIn('invoice_id', $invoices->pluck('id'))->sum('amount');
        $totalDue       = ($totalInvoiced + ($customer->opening_balance ?? 0)) - $totalPaid;

        if ($request->amount > $totalDue) {
            flash(__t('payment_amount_exceeds_due'))->error();
            return back()->withInput();
        }

        try {
            \Illuminate\Support\Facades\Log::info("=== START CUSTOMER PAYMENT ===");
            \Illuminate\Support\Facades\Log::info("Processing payment for Customer: {$customer->full_name} (ID: {$customer->id}), Total Payment Amount: {$request->amount}");
            
            DB::transaction(function () use ($request, $customer) {
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
                        'payment_type' => $request->payment_type,
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

                    $paymentAmount -= $allocation;
                    \Illuminate\Support\Facades\Log::info("Remaining Payment Amount after Invoice {$invoice->id} allocation: {$paymentAmount}");
                }

                if ($paymentAmount > 0) {
                    \Illuminate\Support\Facades\Log::info("Allocating remaining {$paymentAmount} to Opening Balance. Previous Opening Balance: {$customer->opening_balance}");
                    
                    $customer->opening_balance -= $paymentAmount;
                    $customer->save();

                    \Illuminate\Support\Facades\Log::info("New Opening Balance for Customer ID: {$customer->id} is now: {$customer->opening_balance}");

                    // Create Payment Record for Opening Balance
                    InvoicePayment::create([
                        'customer_id'  => $customer->id,
                        'invoice_id'   => null,
                        'date'         => $request->date,
                        'payment_type' => $request->payment_type,
                        'amount'       => $paymentAmount,
                        'notes'        => $request->notes ? $request->notes . " (Opening Balance Payment)" : "Opening Balance Payment",
                        'created_by'   => auth()->id(),
                        'bank_info'    => ['bank_name' => $request->account_id]
                    ]);
                }

                // Add to account balance (total amount)
                $account = Account::findOrFail($request->account_id);
                $account->recordInvoicePayment(
                    (float)$request->amount, 
                    null, // No single invoice reference for the transaction if it's bulk
                    "Customer Bulk Payment: " . $customer->full_name
                );
                
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
        
        $totalInvoiced = Invoice::where('customer_id', $customer->id)->sum('total');
        $totalPaid      = $payments->sum('amount');
        
        $openingBalancePayments = $payments->whereNull('invoice_id')->sum('amount');
        $originalOpeningBalance = ($customer->opening_balance ?? 0) + $openingBalancePayments;
        
        $due            = ($totalInvoiced + $originalOpeningBalance) - $totalPaid;

        set_page_meta(__t('payment_history') . ' — ' . $customer->full_name);

        return view('admin.invoices.payment.customer-history', compact('customer', 'payments', 'totalPaid', 'due', 'totalInvoiced'));
    }
}
