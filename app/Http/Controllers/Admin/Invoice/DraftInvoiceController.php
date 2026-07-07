<?php

namespace App\Http\Controllers\Admin\Invoice;

use Throwable;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Requests\DraftInvoiceRequest;
use App\Services\Invoice\InvoiceService;
use App\Services\Product\ProductService;
use App\Services\Customer\CustomerService;
use App\Services\Warehouse\WarehouseService;
use App\Services\Invoice\DraftInvoiceService;
use App\Services\Product\ProductCategoryService;
use App\DataTables\AdminDraftInvoiceDataTable;

class DraftInvoiceController extends Controller
{
    protected $draftInvoiceService;
    protected $customerService;
    protected $productService;
    protected $categoryService;
    protected $warehouseService;
    protected $invoiceService;

    public function __construct(
        DraftInvoiceService $draftInvoiceService,
        CustomerService $customerService,
        ProductService $productService,
        ProductCategoryService $categoryService,
        WarehouseService $warehouseService,
        InvoiceService $invoiceService
    ) {
        $this->draftInvoiceService = $draftInvoiceService;
        $this->customerService     = $customerService;
        $this->productService      = $productService;
        $this->categoryService     = $categoryService;
        $this->warehouseService    = $warehouseService;
        $this->invoiceService      = $invoiceService;

        $this->middleware(['permission:List Draft Invoice'])->only(['index']);
        $this->middleware(['permission:Add Draft Invoice'])->only(['store']);
        $this->middleware(['permission:Show Draft Invoice'])->only(['show']);
        $this->middleware(['permission:Delete Draft Invoice'])->only(['destroy']);
        $this->middleware(['permission:Convert Draft To Invoice'])->only(['draftToInvoice', 'storeDraftToInvoice']);
    }

    /**
     * Display a listing of all draft invoices.
     */
    public function index(AdminDraftInvoiceDataTable $dataTable)
    {
        set_page_meta(__('custom.draft_invoices'));
        return $dataTable->render('admin.invoices.draft.index');
    }

    /**
     * Store a newly created draft invoice in storage.
     */
    public function store(DraftInvoiceRequest $request)
    {
        $data = $request->validated();

        if (!$data['customer_id']) {
            $data['customer'] = $data['walkin_customer'];
        } else {
            $data['customer_id'] = customer_id($data['customer_id']);
            $customer            = $this->customerService->get($data['customer_id']);
            $data['customer']    = $customer;
        }

        try {
            $draftInvoice = $this->draftInvoiceService->storeOrUpdate($data);
            flash(__('custom.draft_invoice_created_successful'))->success();
            return response()->json(['success' => true, 'invoice' => $draftInvoice->id], 200);
        } catch (Throwable $th) {
            flash(__('custom.draft_invoice_created_failed'))->error();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 422);
        }
    }

    /**
     * Display the specified draft invoice.
     */
    public function show($id)
    {
        $invoice = $this->draftInvoiceService->get($id, ['items']);
        if (!$invoice) {
            flash(__('custom.draft_invoice_not_found'))->error();
            return redirect()->route('admin.draft-invoices.index');
        }

        set_page_meta(__('custom.show_draft_invoice'));
        return view('admin.invoices.draft.show', ['invoice' => $invoice]);
    }

    /**
     * Delete the draft invoice.
     */
    public function destroy($id)
    {
        $draftInvoice = $this->draftInvoiceService->get($id, ['items']);

        if (!$draftInvoice) {
            flash(__('custom.draft_invoice_not_found'))->error();
            return redirect()->route('admin.draft-invoices.index');
        }

        if ($draftInvoice->delete()) {
            flash(__('custom.draft_invoice_deleted_successful'))->success();
        } else {
            flash(__('custom.draft_invoice_deleted_failed'))->error();
        }

        return redirect()->back();
    }

    /**
     * Show the edit form for converting draft to invoice.
     */
    public function draftToInvoice($id)
    {
        $invoice = $this->draftInvoiceService->get($id, ['items']);
        if (!$invoice) {
            flash(__('custom.draft_invoice_not_found'))->error();
            return redirect()->route('admin.draft-invoices.index');
        }

        $wareHouseId    = $invoice->warehouse_id ?? $this->warehouseService->defaultWareHouse()->id;
        $warehouse      = $this->warehouseService->get($wareHouseId);
        $all_status     = $this->invoiceService->getAllStatus();
        $customers      = $this->customerService->getActiveData(null, ['b_country_data', 'b_state_data', 'b_city_data']);
        $product_stocks = $this->productService->wareHouseWiseAllProductStocks(['product', 'variation'], $warehouse);

        $categories = $this->categoryService->getParents('subCategory');

        $formatted_category = [];
        $array_key          = 0;

        foreach ($categories as $category) {
            $formatted_category[$array_key] = [
                'id'   => $category->id,
                'text' => $category->name,
            ];
            $array_key++;
            foreach ($category->subCategory as $subCategory) {
                $formatted_category[$array_key] = [
                    'id'   => $subCategory->id,
                    'text' => ' --' . $subCategory->name,
                ];
                $array_key++;
                foreach ($subCategory->subCategory as $subSubCategory) {
                    $formatted_category[$array_key] = [
                        'id'   => $subSubCategory->id,
                        'text' => ' ---' . $subSubCategory->name,
                    ];
                    $array_key++;
                }
            }
        }

        $accounts = \App\Models\Account::active()->get();
        array_unshift($formatted_category, ['id' => 'all', 'text' => 'All Categories']);

        set_page_meta(__('custom.edit_invoice'));
        return view('admin.invoices.edit', [
            'invoice'        => $invoice,
            'all_status'     => $all_status,
            'customers'      => $customers,
            'product_stocks' => $product_stocks,
            'categories'     => $formatted_category,
            'warehouse'      => $warehouse,
            'accounts'       => $accounts,
        ]);
    }

    /**
     * Convert draft to a real invoice.
     */
    public function storeDraftToInvoice(InvoiceRequest $request, $id)
    {
        $draft_invoice = $this->draftInvoiceService->get($id, ['items']);

        if (!$draft_invoice) {
            flash(__('custom.draft_invoice_not_found'))->error();
            return redirect()->route('admin.draft-invoices.index');
        }

        $data = $request->validated();

        if (!$data['customer_id']) {
            $data['customer'] = $data['walkin_customer'];
        } else {
            $data['customer_id'] = customer_id($data['customer_id']);
            $customer            = $this->customerService->get($data['customer_id']);
            $data['customer']    = $customer;
        }

        try {
            $invoice = $this->invoiceService->storeOrUpdate($data);
            flash(__('custom.invoice_created_successful'))->success();

            $draft_invoice->delete();
            return response()->json(['success' => true, 'invoice' => $invoice->id], 200);
        } catch (Throwable $th) {
            flash(__('custom.invoice_created_failed'))->success();
            return response()->json(['success' => false, 'message' => $th->getMessage()], 422);
        }
    }

    public function download($id)
    {
        return $this->draftInvoiceService->download($id);
    }
}
