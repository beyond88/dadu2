<?php

namespace App\Http\Controllers\Admin\Report;

use App\Exports\ExpiredProductsExport;
use PDF;
use Excel;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Str;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Exports\SalesReportExport;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Exports\ExpensesReportExport;
use App\Exports\PaymentsReportExport;
use App\Exports\PurchasesReportExport;
use App\Services\Report\ReportServices;
use App\Services\Invoice\InvoiceService;
use App\Services\Product\ProductService;
use App\Services\Expenses\ExpensesService;
use App\Services\Purchase\PurchaseServices;
use App\Services\Warehouse\WarehouseService;


class ReportsController extends Controller
{
    protected $expensesService;
    protected $invoiceService;
    protected $purchaseServices;
    protected $productServices;
    protected $reportServices;
    protected $warehouseService;


    /**
     * __construct
     *
     * @return void
     */
    public function __construct(
        ExpensesService $expensesService,
        InvoiceService $invoiceService,
        PurchaseServices $purchaseServices,
        ProductService $productServices,
        ReportServices $reportServices,
        WarehouseService $warehouseService
    ) {
        $this->expensesService = $expensesService;
        $this->invoiceService = $invoiceService;
        $this->purchaseServices = $purchaseServices;
        $this->productServices = $productServices;
        $this->reportServices = $reportServices;
        $this->warehouseService = $warehouseService;


        $this->middleware(['permission:Expenses Report'])->only(['expenses']);
        $this->middleware(['permission:Sales Report'])->only(['sales']);
        $this->middleware(['permission:Purchases Report'])->only(['purchases']);
        $this->middleware(['permission:Payments Report'])->only(['payments']);
        $this->middleware(['permission:Stock Out Report'])->only(['stockOutReport']);
    }


    /**
     * expenses
     *
     * @param  mixed $request
     * @return void
     */
    public function expenses(Request $request)
    {
        $total = 0;
        $data = [];
        $report_range = '';
        $start = $request->from_date;
        $end = $request->to_date;

        if ($start && $end) {
            $report_range = $start . ' - ' . $end;
            $data = $this->expensesService->filterByDateRange($start, $end, ['category']);
        }

        if (isset($request->q) && $request->q = 'all-time') {
            $report_range = 'All Time';
            $data = $this->expensesService->get(null, ['category']);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $total = $data->sum('total');
        }

        set_page_meta(__('custom.expenses_report'));
        return view('admin.reports.expenses', compact('data', 'report_range', 'total'));
    }

    /**
     * exportExpenses
     *
     * @param  mixed $request
     * @return void
     */
    public function exportExpenses(Request $request)
    {
        $total = 0;
        $data = [];
        $report_range = '';
        $start = $request->from_date;
        $end = $request->to_date;
        $type = $request->type;

        if ($start && $end) {
            $report_range = $start . ' - ' . $end;
            $data = $this->expensesService->filterByDateRange($start, $end, ['category']);
        } else {
            $report_range = 'All Time';
            $data = $this->expensesService->get(null, ['category']);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $total = $data->sum('total');
        }


        // return view('admin.reports.pdf.expenses', compact('data', 'report_range', 'total'));

        $name = 'Expenses-report-' . Str::slug($report_range);
        if ($type == 'pdf') {
            $pdf = PDF::loadView('admin.reports.pdf.expenses', ['data' => $data, 'report_range' => $report_range, 'total' => $total]);
            return $pdf->download($name . '.pdf');
        } else if ($type == 'csv') {
            return Excel::download(new ExpensesReportExport($data), $name . '.csv');
        } else if ($type == 'excel') {
            return Excel::download(new ExpensesReportExport($data), $name . '.xlsx');
        }
    }


    /**
     * sales
     *
     * @param  mixed $request
     * @return void
     */
    public function sales(Request $request)
    {
        $wareHouses = Warehouse::query()->pluck('name', 'id');

        $gross_total = 0;
        $total_paid = 0;

        $data = [];
        $report_range = '';
        $start = $request->from_date;
        $end = $request->to_date;

        if ($start && $end) {
            $report_range = $start . ' - ' . $end;
            $data = $this->invoiceService->filterByDateRange($start, $end);
        }

        if (isset($request->q) && $request->q = 'all-time') {
            $report_range = 'All Time';
            $data = $this->invoiceService->filterWareHouseWiseAll(['warehouse']);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $gross_total = $data->sum('total');
            $total_paid = $data->sum('total_paid');
        }


        set_page_meta(__('custom.sales_report'));
        return view('admin.reports.sales', compact('data', 'report_range', 'gross_total', 'total_paid', 'wareHouses'));
    }


    /**
     * exportSales
     *
     * @param  mixed $request
     * @return void
     */
    public function exportSales(Request $request)
    {
        $gross_total = 0;
        $total_paid = 0;

        $data = [];
        $report_range = '';
        $start = $request->from_date;
        $end = $request->to_date;
        $type = $request->type;

        if ($start && $end) {
            $report_range = $start . ' - ' . $end;
            $data = $this->invoiceService->filterByDateRange($start, $end);
        } else {
            $report_range = 'All Time';
            $data = $this->invoiceService->get(null);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $gross_total = $data->sum('total');
            $total_paid = $data->sum('total_paid');
        }



        // return view('admin.reports.pdf.sales', compact('data', 'report_range', 'gross_total', 'total_paid'));

        $name = 'Sales-report-' . Str::slug($report_range);
        if ($type == 'pdf') {
            $pdf = PDF::loadView('admin.reports.pdf.sales', ['data' => $data, 'report_range' => $report_range, 'gross_total' => $gross_total, 'total_paid' => $total_paid]);
            return $pdf->download($name . '.pdf');
        } else if ($type == 'csv') {
            return Excel::download(new SalesReportExport($data), $name . '.csv');
        } else if ($type == 'excel') {
            return Excel::download(new SalesReportExport($data), $name . '.xlsx');
        }
    }

    /**
     * purchases
     *
     * @param  mixed $request
     * @return void
     */
    public function purchases(Request $request)
    {
        $wareHouses = Warehouse::query()->pluck('name', 'id');

        $total = 0;
        $data = [];
        $report_range = '';
        $start = $request->from_date;
        $end = $request->to_date;

        if ($start && $end) {
            $report_range = $start . ' - ' . $end;
            $data = $this->purchaseServices->filterByDateRange($start, $end, ['purchaseItems', 'warehouse']);
        }

        if (isset($request->q) && $request->q = 'all-time') {
            $report_range = 'All Time';
            $data = $this->purchaseServices->allTime(['purchaseItems', 'warehouse']);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $total = $data->sum('total');
        }

//        return $data;

        set_page_meta(__('custom.purchases_report'));
        return view('admin.reports.purchases', compact('data', 'report_range', 'total', 'wareHouses'));
    }


    /**
     * exportPurchases
     *
     * @param  mixed $request
     * @return void
     */
    public function exportPurchases(Request $request)
    {
        $total = 0;
        $data = [];
        $report_range = '';
        $start = $request->from_date;
        $end = $request->to_date;
        $type = $request->type;

        if ($start && $end) {
            $report_range = $start . ' - ' . $end;
            $data = $this->purchaseServices->filterByDateRange($start, $end);
        } else {
            $report_range = 'All Time';
            $data = $this->purchaseServices->get(null);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $total = $data->sum('total');
        }


        // return view('admin.reports.pdf.purchases', compact('data', 'report_range', 'total'));

        $name = 'Purchase-report-' . Str::slug($report_range);
        if ($type == 'pdf') {
            $pdf = PDF::loadView('admin.reports.pdf.purchases', ['data' => $data, 'report_range' => $report_range, 'total' => $total]);
            return $pdf->download($name . '.pdf');
        } else if ($type == 'csv') {
            return Excel::download(new PurchasesReportExport($data), $name . '.csv');
        } else if ($type == 'excel') {
            return Excel::download(new PurchasesReportExport($data), $name . '.xlsx');
        }
    }


    /**
     * payments
     *
     * @param  mixed $request
     * @return void
     */
    public function payments(Request $request)
    {
        $wareHouses = Warehouse::query()->pluck('name', 'id');

        $total = 0;
        $data = [];
        $report_range = '';
        $start = $request->from_date;
        $end = $request->to_date;


        if ($start && $end) {
            $report_range = $start . ' - ' . $end;
            $data = $this->invoiceService->filterPaymentByDateRange($start, $end, ['invoice.warehouse']);
        }

        if (isset($request->q) && $request->q = 'all-time') {
            $report_range = 'All Time';
            $data = $this->invoiceService->getAllPayments(['invoice.warehouse']);
        }

        // Calculate total
        if ($data instanceof Collection) {
            $total = $data->sum('amount');
        }

        set_page_meta(__('custom.payments_report'));
        return view('admin.reports.payments', compact('data', 'report_range', 'total', 'wareHouses'));
    }


    /**
     * exportPayments
     *
     * @param  mixed $request
     * @return void
     */
    public function exportPayments(Request $request)
    {
        $total = 0;
        $data = [];
        $report_range = '';
        $start = $request->from_date;
        $end = $request->to_date;
        $type = $request->type;

        if ($start && $end) {
            $report_range = $start . ' - ' . $end;
            $data = $this->invoiceService->filterPaymentByDateRange($start, $end);
        } else {
            $report_range = 'All Time';
            $data = $this->invoiceService->getAllPayments();
        }
        // Calculate total
        if ($data instanceof Collection) {
            $total = $data->sum('amount');
        }


        // return view('admin.reports.pdf.payments', compact('data', 'report_range', 'total'));

        $name = 'Payment-report-' . Str::slug($report_range);
        if ($type == 'pdf') {
            $pdf = PDF::loadView('admin.reports.pdf.payments', ['data' => $data, 'report_range' => $report_range, 'total' => $total]);
//            return $pdf->download($name . '.pdf');
            return $pdf->download($name . '.pdf',array("Attachment" => false));
        } else if ($type == 'csv') {
            return Excel::download(new PaymentsReportExport($data), $name . '.csv');
        } else if ($type == 'excel') {
            return Excel::download(new PaymentsReportExport($data), $name . '.xlsx');
        }
    }


    public function warehouseStock(Request $request)
    {

        set_page_meta(__('custom.warehouse_stock_report'));

        $report_range = "All time";

        $products = $this->productServices->productAllWarehouseStock();
        $warehouses = Warehouse::query()->pluck('name', 'id');
//        dd($warehouses);

        return view('admin.reports.warehouse-stock', compact('report_range', 'products', 'warehouses'));
    }
    public function expiredProducts(Request $request)
    {
        set_page_meta(__('custom.expired_product_report'));

        $query = ProductStock::with(['product', 'warehouse'])
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now());
        $type = $request->type;


        $report_range = 'All time';

        if ($request->filled('warehouse')) {
            $query->where('warehouse_id', $request->warehouse);
        }

        if ($request->has('q') && $request->q == 'all-time') {
            $report_range = 'All time';
        } elseif ($request->filled('from_date') && $request->filled('to_date')) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('expiry_date', [$from, $to]);
            $report_range = $from->format('d M Y') . ' - ' . $to->format('d M Y');
        }

        $expiredProductStocks = $query->orderBy('expiry_date', 'desc')->get();
        $name = 'Expired-product-report-' . Str::slug($report_range);

        $warehouses = Warehouse::pluck('name', 'id');
        if ($type == 'pdf') {
            $pdf = PDF::loadView('admin.reports.pdf.expired-products', ['expiredProductStocks' => $expiredProductStocks, 'report_range' => $report_range, 'warehouses' => $warehouses]);
//            return $pdf->download($name . '.pdf');
            return $pdf->download($name . '.pdf',array("Attachment" => false));
        } else if ($type == 'csv') {
            return Excel::download(new ExpiredProductsExport($expiredProductStocks), $name . '.csv');
        } else if ($type == 'excel') {
            return Excel::download(new ExpiredProductsExport($expiredProductStocks), $name . '.xlsx');
        }else{
            return view('admin.reports.expired-products',['expiredProductStocks' => $expiredProductStocks, 'report_range' => $report_range, 'warehouses' => $warehouses]);
        }

    }

    public function warehousePrice(Request $request)
    {
        $warehouses = Warehouse::with('product_stocks.product')->get();

        $data = [];
        foreach ($warehouses as $warehouse) {
            $totalPrice = 0;
            $totalQuantity = 0;

            foreach ($warehouse->product_stocks as $productStock) {
                $totalPrice += optional($productStock->product)->price * $productStock->quantity;
                $totalQuantity += $productStock->quantity;
            }
            $data [$warehouse->name] = [$totalPrice, $totalQuantity];
            // dump("Total price for {$warehouse->name}: {$totalPrice}");
        }
        // dd($data);
     return view('admin.reports.warehouse-price', compact('data'));
    }

    public function lossProfit(Request $request)
    {
        try {
            set_page_meta(__('custom.loss_profit_report'));

            $wareHouses = Warehouse::query()->pluck('name', 'id');

            $report_range = '';
            $start = $request->from_date;
            $end = $request->to_date;

            if ($start && $end) {
                $report_range = $start . ' - ' . $end;
            }

            if (isset($request->q) && $request->q = 'all-time') {
                $report_range = 'All Time';
            }

            $loss_profit_data = $this->reportServices->lossProfitCalculation($request);

            return view('admin.reports.loss-profit', compact('wareHouses', 'loss_profit_data', 'report_range'));
        } catch (\Exception $e) {
            logger($e->getMessage());
            flash(__('custom.something_went_wrong_maybe_your_product_stock_not_created_yet'))->error();

            return redirect()->back();
        }
    }

    /**
     * balance
     *
     * Category-wise balance (sale volume) report. For every single day between
     * the from/to date, shows how much was sold (in amount) per product category.
     *
     * @param  mixed $request
     * @return void
     */
    public function balance(Request $request)
    {
        set_page_meta(__('custom.balance_report'));

        $data = $this->buildBalanceData($request->from_date, $request->to_date);

        return view('admin.reports.balance', $data);
    }

    /**
     * exportBalance
     *
     * Export the category-wise balance report as PDF, CSV or Excel.
     *
     * @param  mixed $request
     * @return void
     */
    public function exportBalance(Request $request)
    {
        $data = $this->buildBalanceData($request->from_date, $request->to_date);
        $type = $request->type;

        $name = 'Balance-report-' . Str::slug($data['report_range'] ?: 'all');

        if ($type == 'pdf') {
            $pdf = PDF::loadView('admin.reports.pdf.balance', $data)
                ->setPaper('a4', 'landscape');
            return $pdf->download($name . '.pdf');
        } elseif ($type == 'csv') {
            return Excel::download(new \App\Exports\BalanceReportExport($data), $name . '.csv');
        } elseif ($type == 'excel') {
            return Excel::download(new \App\Exports\BalanceReportExport($data), $name . '.xlsx');
        }

        return redirect()->back();
    }

    /**
     * buildBalanceData
     *
     * Builds the category-wise daily balance matrix for the given date range.
     *
     * @param  string|null $start
     * @param  string|null $end
     * @return array
     */
    protected function buildBalanceData($start = null, $end = null): array
    {
        // Columns = product categories (e.g. Bulb, LED, Bakelite ...)
        $categories = \App\Models\ProductCategory::orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);

        $report_range   = '';
        $dates          = [];      // ordered list of every day in the range
        $rows           = [];      // 'Y-m-d' => [category_id => amount]
        $categoryTotals = [];      // category_id => total amount
        $grandTotal     = 0;
        $expenseRows    = [];      // [['name' => ..., 'total' => ...], ...]

        // Pre-seed category totals so every column has a value.
        foreach ($categories as $cat) {
            $categoryTotals[$cat->id] = 0;
        }

        if ($start && $end) {
            $report_range = $start . ' - ' . $end;

            // Raw expressions are not auto-prefixed, so add the table prefix manually.
            $p = \DB::getTablePrefix();

            // Aggregate the sold amount (line sub_total) per day, per category.
            $results = \DB::table('invoice_items')
                ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                ->join('products', 'products.id', '=', 'invoice_items.product_id')
                ->whereDate('invoices.date', '>=', $start)
                ->whereDate('invoices.date', '<=', $end)
                ->whereNotNull('products.category_id')
                ->when(request('warehouse'), fn($q) => $q->where('invoices.warehouse_id', request('warehouse')))
                ->selectRaw("DATE({$p}invoices.date) as sale_date, {$p}products.category_id as category_id, SUM({$p}invoice_items.sub_total) as amount")
                ->groupBy('sale_date', 'products.category_id')
                ->get();

            // Build an ordered row for every single day in the range.
            $period = \Carbon\CarbonPeriod::create($start, $end);
            foreach ($period as $day) {
                $key          = $day->format('Y-m-d');
                $dates[]      = $key;
                $rows[$key]   = [];
            }

            foreach ($results as $r) {
                $day = $r->sale_date;

                // Guard against any day that falls outside the seeded period.
                if (!isset($rows[$day])) {
                    $rows[$day] = [];
                    $dates[]    = $day;
                }

                $amount = (float) $r->amount;
                $rows[$day][$r->category_id] = ($rows[$day][$r->category_id] ?? 0) + $amount;
                $categoryTotals[$r->category_id] = ($categoryTotals[$r->category_id] ?? 0) + $amount;
                $grandTotal += $amount;
            }

            // Bottom ledger rows (Salary, Bonus, EXP., REP. ...) pulled from the
            // system Expenses, summed per expense-category for the same range.
            $expenseSums = \DB::table('expenses')
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->select('category_id', \DB::raw('SUM(total) as amount'))
                ->groupBy('category_id')
                ->pluck('amount', 'category_id');

            $expenseCategories = \App\Models\ExpensesCategory::orderBy('name')->get(['id', 'name']);
            foreach ($expenseCategories as $ec) {
                $expenseRows[] = [
                    'name'  => $ec->name,
                    'total' => (float) ($expenseSums[$ec->id] ?? 0),
                ];
            }
        }

        return compact(
            'categories',
            'dates',
            'rows',
            'categoryTotals',
            'grandTotal',
            'report_range',
            'expenseRows'
        );
    }

    public function stockOutReport(Request $request)
    {
        set_page_meta(__('custom.stock_out_report'));

        $query = Product::with(['allStock.warehouse', 'category', 'brand'])
            ->where('status', Product::STATUS_ACTIVE);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        $all_products = $query->get();
        $products = $all_products->filter(function($product) {
            return $product->allStock->sum('quantity') <= 0;
        });

        $categories = \App\Models\ProductCategory::pluck('name', 'id');
        $brands = \App\Models\Brand::pluck('name', 'id');

        return view('admin.reports.stock-out', compact('products', 'categories', 'brands'));
    }
}
