<?php

namespace App\Services\Employee;


use App\Models\Invoice;
use App\Services\BaseService;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

/**
 * CustomerService
 */
class EmployeeService extends BaseService
{
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(Customer $model)
    {
        parent::__construct($model);
    }

    public function customerShowDetails($customer, $request = null)
    {
        $invoiceSearch = $request ? $request->get('invoice_search', '') : '';
        $productSearch = $request ? $request->get('product_search', '') : '';
        $invoicePage = $request ? $request->get('invoice_page', 1) : 1;
        $productPage = $request ? $request->get('product_page', 1) : 1;

        $invoices = $this->getInvoicesWithPagination($customer->id, $invoiceSearch, $invoicePage, $productSearch, $productPage);
        $products = $this->getProductsWithPagination($customer->id, $productSearch, $productPage, $invoiceSearch, $invoicePage);

        return [
            'customer' => $customer,
            'invoices' => $invoices,
            'products' => $products,
            'invoiceSearch' => $invoiceSearch,
            'productSearch' => $productSearch,
        ];
    }

    public function getAllEmployeeInvoices($employeeId, $search = '')
    {
        $query = Invoice::query()
            ->with(['items.product', 'payments', 'customerInfo'])
            ->latest('date')
            ->where('customer_id', $employeeId);

        if (!empty($search)) {
            $numericSearch = ltrim($search, '0');
            if ($numericSearch === '') {
                $numericSearch = '0';
            }

            $query->where(function ($q) use ($search, $numericSearch) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhere('id', $numericSearch)
                    ->orWhere('id', 'like', '%' . $numericSearch . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('total', 'like', '%' . $search . '%');
            });
        }

        return $query->get();
    }

    public function getInvoicesWithPagination($employeeId, $search = '', $page = 1, $productSearch = '', $productPage = 1)
    {
        $query = Invoice::query()
            ->with(['items.product', 'payments', 'customerInfo'])
            ->latest('date')
            ->where('customer_id', $employeeId);

        if (!empty($search)) {
            $numericSearch = ltrim($search, '0');
            if ($numericSearch === '') {
                $numericSearch = '0';
            }

            $query->where(function ($q) use ($search, $numericSearch) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhere('id', $numericSearch)
                    ->orWhere('id', 'like', '%' . $numericSearch . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('total', 'like', '%' . $search . '%');
            });
        }

        return $query->paginate(10, ['*'], 'invoice_page', $page)
            ->appends([
                'tab' => 'invoice',
                'product_search' => $productSearch,
                'product_page' => $productPage,
                'invoice_search' => $search
            ]);
    }

    public function getAllEmployeeProducts($employeeId, $search = '')
    {
        $query = \App\Models\InvoiceItem::query()
            ->with('product')
            ->whereHas('invoice', function ($q) use ($employeeId) {
                $q->where('customer_id', $employeeId);
            });

        if (!empty($search)) {
            $numericSearch = ltrim($search, '0');
            if ($numericSearch === '') {
                $numericSearch = '0';
            }
            
            $query->where(function ($q) use ($search, $numericSearch) {
                $q->where('product_id', $search)
                    ->orWhere('product_id', $numericSearch)
                    ->orWhere('product_id', 'like', '%' . $numericSearch . '%')
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'like', '%' . $search . '%')
                            ->orWhere('sku', 'like', '%' . $search . '%');
                    });
            });
        }

        return $query->get()
            ->groupBy('product_id')
            ->map(function ($items) {
                $first = $items->first();
                return [
                    'product_id' => $first->product_id,
                    'product_name' => $first->product->name ?? 'N/A',
                    'sku' => $first->product->sku ?? 'N/A',
                    'price' => $first->product->price ?? 0,
                    'quantity' => $items->sum('quantity'),
                ];
            })
            ->values();
    }

    public function getProductsWithPagination($employeeId, $search = '', $page = 1, $invoiceSearch = '', $invoicePage = 1)
    {
        $products = $this->getAllEmployeeProducts($employeeId, $search);

        $perPage = 10;
        $currentPage = $page;
        $total = $products->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedProducts = $products->slice($offset, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedProducts,
            $total,
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Support\Facades\Request::url(), 'query' => \Illuminate\Support\Facades\Request::query()]
        );

        return $paginator->setPageName('product_page')
            ->appends([
                'tab' => 'product',
                'invoice_search' => $invoiceSearch,
                'invoice_page' => $invoicePage,
                'product_search' => $search
            ]);
    }

    public function updateProfile(array $data, $id)
    {
        try {
            // Update
            $user = $this->get($id);

            // Password
            if (isset($data['password']) && $data['password']) {
                $user->password = Hash::make($data['password']);
            }

            // Avatar
            if (isset($data['avatar']) && $data['avatar'] != null) {
                $user->avatar = $this->uploadFile($data['avatar'], $user->avatar);
            }

            $user->first_name   = $data['first_name'];
            $user->last_name    = $data['last_name'];
            $user->phone        = $data['phone'];
            $user->email        = $data['email'];

            // Update user
            return $user->save();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function all()
    {
        return $this->model->orderBy('first_name', 'asc')->paginate(10);
    }
}
