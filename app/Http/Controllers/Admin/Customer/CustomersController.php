<?php

namespace App\Http\Controllers\Admin\Customer;

use App\DataTables\CustomerDataTable;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\SystemCountry;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Services\Customer\CustomerService;
use Illuminate\Support\Facades\Hash;
use PDF;

class CustomersController extends Controller
{
    protected $customerService;

    /**
     * __construct
     *
     * @param  mixed $customerService
     * @return void
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;

        $this->middleware(['permission:List Customer'])->only(['index']);
        $this->middleware(['permission:Add Customer'])->only(['create']);
        $this->middleware(['permission:Edit Customer'])->only(['edit']);
        $this->middleware(['permission:Delete Customer'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CustomerDataTable $dataTable)
    {

        set_page_meta(__('custom.customers'));
        return $dataTable->render('admin.customers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // TODO: convert to service
        $countries = SystemCountry::get();

        set_page_meta(__('custom.add_customer'));
        return view('admin.customers.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $data = $request->validated();

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($this->customerService->createOrUpdateWithFile($data, 'avatar')) {
            flash(__('custom.customer_create_successful'))->success();
        } else {
            flash(__('custom.customer_create_failed'))->error();
        }

        return redirect()->route('admin.customers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  App\Models\Customer $customer
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer, \Illuminate\Http\Request $request)
    {
        set_page_meta(__('custom.customer_details'));

        return view('admin.customers.show', $this->customerService->customerShowDetails($customer, $request));
    }

    /**
     * Export Invoice History
     */
    public function exportInvoicesHistory($id, \Illuminate\Http\Request $request)
    {
        $customer = Customer::findOrFail($id);
        $search = $request->get('invoice_search', '');
        $invoices = $this->customerService->getAllCustomerInvoices($id, $search);

        $type = $request->get('type', 'pdf');
        $fileName = 'Invoice_History_' . $customer->full_name . '_' . date('YmdHis');

        if ($type == 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CustomerInvoiceHistoryExport($invoices), $fileName . '.xlsx');
        } elseif ($type == 'pdf') {
            $pdf = PDF::loadView('customer.employee.pdf.invoice-history', compact('invoices', 'customer'));
            return $pdf->download($fileName . '.pdf');
        }

        return back();
    }

    /**
     * Export To Pay History
     */
    public function exportToPayHistory($id, \Illuminate\Http\Request $request)
    {
        $customer = Customer::findOrFail($id);
        $search = $request->get('topay_search', '');
        $invoices = $this->customerService->getAllCustomerNotPaidInvoices($id, $search);

        $type = $request->get('type', 'pdf');
        $fileName = 'ToPay_History_' . $customer->full_name . '_' . date('YmdHis');

        if ($type == 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CustomerInvoiceHistoryExport($invoices), $fileName . '.xlsx');
        } elseif ($type == 'pdf') {
            $pdf = PDF::loadView('customer.employee.pdf.invoice-history', compact('invoices', 'customer'));
            return $pdf->download($fileName . '.pdf');
        }

        return back();
    }

    /**
     * Export Product History
     */
    public function exportProductsHistory($id, \Illuminate\Http\Request $request)
    {
        $customer = Customer::findOrFail($id);
        $search = $request->get('product_search', '');
        $products = $this->customerService->getAllCustomerProducts($id, $search);

        $type = $request->get('type', 'pdf');
        $fileName = 'Product_History_' . $customer->full_name . '_' . date('YmdHis');

        if ($type == 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CustomerProductHistoryExport($products), $fileName . '.xlsx');
        } elseif ($type == 'pdf') {
            $pdf = PDF::loadView('customer.employee.pdf.products-history', compact('products', 'customer'));
            return $pdf->download($fileName . '.pdf');
        }

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = $this->customerService->get($id);
        if (!$customer) abort(404);

        $countries = SystemCountry::get();

        set_page_meta(__('custom.edit_customer'));
        return view('admin.customers.edit', compact('customer', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        // dd($request->all());
        $data = $request->validated();
        // dd($data);

        if (!isset($data['billing_same'])) {
            $data['billing_same'] = 0;
        }
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($this->customerService->createOrUpdateWithFile($data, 'avatar', $id)) {
            flash(__('custom.customer_updated_successful'))->success();
        } else {
            flash(__('custom.customer_updated_failed'))->error();
        }

        return redirect()->route('admin.customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if ($this->customerService->delete($id)) {
                flash(__('custom.customer_deleted_successful'))->success();
            } else {
                flash(__('custom.customer_deleted_failed'))->error();
            }
        } catch (\Throwable $th) {
            flash(__('custom.this_record_already_used'))->warning();
        }

        return redirect()->route('admin.customers.index');
    }
    public function verifyUnverify($id)
    {
        $customer = $this->customerService->get($id);
        if (!$customer) abort(404);
        if ($customer->is_verified == Customer::STATUS_VERIFIED) {
            $customer->is_verified = Customer::STATUS_UNVERIFIED;
        } else {
            $customer->is_verified = Customer::STATUS_VERIFIED;
        }
        $customer->save();
        return redirect()->route('admin.customers.index');
    }
}
