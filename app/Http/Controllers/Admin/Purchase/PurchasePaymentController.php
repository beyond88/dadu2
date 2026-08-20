<?php

namespace App\Http\Controllers\Admin\Purchase;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Account;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PurchasePaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Add Purchase Payment'])->only(['create', 'store']);
        $this->middleware(['permission:View Purchase Payment'])->only(['history']);
        $this->middleware(['permission:Delete Purchase Payment'])->only(['destroy']);
    }

    /**
     * Show the make-payment form for a purchase.
     */
    public function create(Purchase $purchase)
    {
        $totalPaid = $purchase->payments()->sum('amount');
        $due       = $purchase->total - $totalPaid;
        $accounts  = Account::active()->get();

        set_page_meta(__t('make_payment') . ' — #' . $purchase->purchase_number);

        return view('admin.purchase.payment.create', compact('purchase', 'totalPaid', 'due', 'accounts'));
    }

    /**
     * Store a new payment against the purchase.
     */
    public function store(Request $request, Purchase $purchase)
    {
        $request->validate([
            'date'         => 'required|date',
            'payment_type' => 'nullable|string|max:100',
            'account_id'   => 'required|exists:accounts,id',
            'amount'       => 'required|numeric|min:0.01',
            'notes'        => 'nullable|string|max:500',
        ]);

        $totalPaid = $purchase->payments()->sum('amount');
        $due       = $purchase->total - $totalPaid;

        if ($request->amount > $due) {
            flash(__t('payment_amount_exceeds_due'))->error();
            return back()->withInput();
        }

        try {
            DB::transaction(function () use ($request, $purchase) {
                // 1. Create Purchase Payment Record
                PurchasePayment::create([
                    'purchase_id'  => $purchase->id,
                    'account_id'   => $request->account_id,
                    'date'         => $request->date,
                    'payment_type' => $request->payment_type,
                    'amount'       => $request->amount,
                    'notes'        => $request->notes,
                    'created_by'   => auth()->id(),
                ]);

                // 2. Deduct From Account
                $account = Account::findOrFail($request->account_id);
                $account->reduceBalance(
                    $request->amount, 
                    "Payment for Purchase #" . $purchase->purchase_number,
                    $purchase->id,
                    'purchase'
                );
            });

            flash(__t('payment_added_successful'))->success();
            return redirect()->route('admin.purchases.show', $purchase->id);
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return back()->withInput();
        }
    }

    /**
     * Show payment history for a purchase.
     */
    public function history(Purchase $purchase)
    {
        $payments  = $purchase->payments()->with('createdBy')->latest()->get();
        $totalPaid = $payments->sum('amount');
        $due       = $purchase->total - $totalPaid;

        set_page_meta(__t('payment_history') . ' — #' . $purchase->purchase_number);

        return view('admin.purchase.payment.history', compact('purchase', 'payments', 'totalPaid', 'due'));
    }

    /**
     * Delete a specific payment.
     */
    public function destroy(PurchasePayment $payment)
    {
        $purchaseId = $payment->purchase_id;
        $payment->delete();

        flash(__t('payment_deleted_successful'))->success();
        return redirect()->route('admin.purchases.show', $purchaseId);
    }

    /**
     * Show the make-payment form for a supplier (Supplier individual payment)
     */
    public function createSupplierPayment(Supplier $supplier)
    {
        // Get all purchases for this supplier
        $purchases = Purchase::where('supplier_id', $supplier->id)->get();

        // Same figures as the supplier list: the opening balance is part of what
        // is owed (or already advanced), so it has to be in here too — otherwise
        // this page caps payments below the real due.
        $purchaseTotal = (float) $purchases->sum('total');
        $paymentTotal  = (float) PurchasePayment::whereIn('purchase_id', $purchases->pluck('id'))->sum('amount');

        $totalPurchased = $purchaseTotal + $supplier->openingDebt();
        $totalPaid      = $paymentTotal + $supplier->openingAdvance();
        $due            = max(0, $supplier->totalDue($purchaseTotal, $paymentTotal));
        $accounts       = Account::active()->get();

        set_page_meta(__t('make_payment') . ' — ' . $supplier->full_name);

        return view('admin.purchase.payment.supplier-payment', compact('supplier', 'totalPaid', 'due', 'accounts', 'totalPurchased'));
    }

    /**
     * Store payment for a supplier (FIFO across unpaid purchases)
     */
    public function storeSupplierPayment(Request $request, Supplier $supplier)
    {
        $request->validate([
            'date'         => 'required|date',
            'payment_type' => 'nullable|string|max:100',
            'account_id'   => 'required|exists:accounts,id',
            'amount'       => 'required|numeric|min:0.01',
            'notes'        => 'nullable|string|max:500',
        ]);

        // Total due for validation — includes the opening balance, so it matches
        // the figure shown on the supplier list and on this page.
        $purchases     = Purchase::where('supplier_id', $supplier->id)->get();
        $purchaseTotal = (float) $purchases->sum('total');
        $paymentTotal  = (float) PurchasePayment::whereIn('purchase_id', $purchases->pluck('id'))->sum('amount');
        $totalDue      = $supplier->totalDue($purchaseTotal, $paymentTotal);

        if ($request->amount > $totalDue) {
            flash(__t('payment_amount_exceeds_due'))->error();
            return back()->withInput();
        }

        try {
            DB::transaction(function () use ($request, $supplier) {
                $paymentAmount = $request->amount;
                
                // Get unpaid or partially paid purchases for this supplier (FIFO)
                $unpaidPurchases = Purchase::where('supplier_id', $supplier->id)
                    ->get()
                    ->filter(function($p) {
                        return $p->total > $p->payments()->sum('amount');
                    });

                foreach ($unpaidPurchases as $purchase) {
                    if ($paymentAmount <= 0) break;

                    $purchasePaid = $purchase->payments()->sum('amount');
                    $purchaseDue  = $purchase->total - $purchasePaid;
                    
                    $allocation = min($paymentAmount, $purchaseDue);

                    // Create Payment Record
                    PurchasePayment::create([
                        'purchase_id'  => $purchase->id,
                        'account_id'   => $request->account_id,
                        'date'         => $request->date,
                        'payment_type' => $request->payment_type,
                        'amount'       => $allocation,
                        'notes'        => $request->notes . " (Bulk Supplier Payment)",
                        'created_by'   => auth()->id(),
                    ]);

                    $paymentAmount -= $allocation;
                }

                // Whatever is left over settles the opening balance debt. That
                // debt predates every purchase, so it has no purchase row to
                // hang a payment on — it is cleared by moving opening_balance
                // back towards zero instead, which keeps the money the account
                // was debited fully accounted for.
                if ($paymentAmount > 0 && $supplier->openingDebt() > 0) {
                    $settled = min($paymentAmount, $supplier->openingDebt());
                    $supplier->opening_balance = round((float) $supplier->opening_balance + $settled, 2);
                    $supplier->save();
                    $paymentAmount -= $settled;
                }

                // Deduct from account once (total amount)
                $account = Account::findOrFail($request->account_id);
                $account->reduceBalance(
                    $request->amount, 
                    "Supplier Payment: " . $supplier->full_name,
                    $supplier->id,
                    'supplier'
                );
            });

            flash(__t('payment_added_successful'))->success();
            return redirect()->route('admin.suppliers.index');
        } catch (\Exception $e) {
            flash($e->getMessage())->error();
            return back()->withInput();
        }
    }

    /**
     * Show payment history for a supplier
     */
    public function supplierPaymentHistory(Supplier $supplier)
    {
        $purchaseIds = Purchase::where('supplier_id', $supplier->id)->pluck('id');
        $payments    = PurchasePayment::whereIn('purchase_id', $purchaseIds)
                        ->with(['createdBy', 'purchase'])
                        ->latest()
                        ->get();
        
        $totalPurchased = Purchase::where('supplier_id', $supplier->id)->sum('total');
        $totalPaid      = $payments->sum('amount');
        $due            = $totalPurchased - $totalPaid;

        set_page_meta(__t('payment_history') . ' — ' . $supplier->full_name);

        return view('admin.purchase.payment.supplier-history', compact('supplier', 'payments', 'totalPaid', 'due', 'totalPurchased'));
    }
}
