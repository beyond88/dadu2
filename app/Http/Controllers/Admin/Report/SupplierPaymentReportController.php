<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\PurchasePayment;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierPaymentReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Supplier Payment History']);
    }

    public function index(Request $request)
    {
        set_page_meta(__('custom.supplier_payment_history'));

        $suppliers = Supplier::orderBy('name')->get();
        
        $query = PurchasePayment::with(['purchase.supplier', 'purchase'])
            ->select('purchase_payments.*')
            ->join('purchases', 'purchase_payments.purchase_id', '=', 'purchases.id')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->when($request->supplier_id, function ($q) use ($request) {
                return $q->where('purchases.supplier_id', $request->supplier_id);
            })
            ->when($request->from_date && $request->to_date, function ($q) use ($request) {
                return $q->whereBetween('purchase_payments.date', [
                    $request->from_date,
                    $request->to_date
                ]);
            })
            ->when($request->payment_type, function ($q) use ($request) {
                return $q->where('purchase_payments.payment_type', $request->payment_type);
            })
            ->orderByDesc('purchase_payments.date')
            ->orderByDesc('purchase_payments.created_at');

        $payments = $query->paginate(25);
        
        // Calculate summary
        $summaryQuery = PurchasePayment::query()
            ->select(
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(DISTINCT purchase_id) as total_invoices')
            )
            ->join('purchases', 'purchase_payments.purchase_id', '=', 'purchases.id')
            ->when($request->supplier_id, function ($q) use ($request) {
                return $q->where('purchases.supplier_id', $request->supplier_id);
            })
            ->when($request->from_date && $request->to_date, function ($q) use ($request) {
                return $q->whereBetween('purchase_payments.date', [
                    $request->from_date,
                    $request->to_date
                ]);
            });

        $summary = $summaryQuery->first();

        // Get supplier summary if specific supplier selected
        $supplierSummary = null;
        if ($request->supplier_id) {
            $supplierSummary = $this->getSupplierSummary($request->supplier_id);
        }

        // Get all payment types for filter
        $paymentTypes = PurchasePayment::distinct()->pluck('payment_type');

        return view('admin.reports.supplier-payment-history', compact(
            'payments',
            'suppliers',
            'summary',
            'supplierSummary',
            'paymentTypes'
        ));
    }

    private function getSupplierSummary($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        
        // Total purchases
        $totalPurchases = Purchase::where('supplier_id', $supplierId)->sum('total');
        
        // Total payments made
        $totalPaid = PurchasePayment::whereHas('purchase', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        })->sum('amount');
        
        // Total due
        $totalDue = Purchase::where('supplier_id', $supplierId)
            ->withSum('payments', 'amount')
            ->get()
            ->sum(function ($purchase) {
                return $purchase->total - ($purchase->payments_sum_amount ?? 0);
            });
        
        // Total invoices
        $totalInvoices = Purchase::where('supplier_id', $supplierId)->count();
        
        // Paid invoices
        $paidInvoices = Purchase::where('supplier_id', $supplierId)
            ->whereHas('payments')
            ->count();
        
        return [
            'supplier' => $supplier,
            'total_purchases' => $totalPurchases,
            'total_paid' => $totalPaid,
            'total_due' => $totalDue,
            'total_invoices' => $totalInvoices,
            'paid_invoices' => $paidInvoices,
        ];
    }

    public function export(Request $request)
    {
        $type = $request->type;
        $payments = PurchasePayment::with(['purchase.supplier', 'purchase'])
            ->join('purchases', 'purchase_payments.purchase_id', '=', 'purchases.id')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->when($request->supplier_id, function ($q) use ($request) {
                return $q->where('purchases.supplier_id', $request->supplier_id);
            })
            ->when($request->from_date && $request->to_date, function ($q) use ($request) {
                return $q->whereBetween('purchase_payments.date', [
                    $request->from_date,
                    $request->to_date
                ]);
            })
            ->orderByDesc('purchase_payments.date')
            ->get(['purchase_payments.*', 'suppliers.name as supplier_name']);

        if ($type == 'pdf') {
            $pdf = \PDF::loadView('admin.reports.pdf.supplier-payment-history', compact('payments'));
            return $pdf->download('supplier-payment-history.pdf');
        } elseif ($type == 'excel') {
            return \Excel::download(
                new \App\Exports\SupplierPaymentHistoryExport($payments),
                'supplier-payment-history.xlsx'
            );
        } else {
            return back()->with('error', __('custom.invalid_export_type'));
        }
    }
}
