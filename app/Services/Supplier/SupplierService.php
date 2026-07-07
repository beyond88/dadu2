<?php

namespace App\Services\Supplier;

use App\Models\Purchase;
use App\Services\BaseService;
use App\Models\Supplier;

/**
 * SupplierService
 */
class SupplierService extends BaseService
{
    /**
     * __construct
     *
     * @param  mixed $model
     * @return void
     */
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    public function supplierShowDetails($supplier, $request = null)
    {
        $purchaseSearch = $request ? $request->get('purchase_search', '') : '';
        $productSearch = $request ? $request->get('product_search', '') : '';
        $purchasePage = $request ? $request->get('purchase_page', 1) : 1;
        $productPage = $request ? $request->get('product_page', 1) : 1;
        $paymentPage = $request ? $request->get('payment_page', 1) : 1;
        $paymentSearch = $request ? $request->get('payment_search', '') : '';
        $tab = $request ? $request->get('tab', 'purchase') : 'purchase';

        // Build purchase query with search
        $purchaseQuery = Purchase::query()
            ->with(['purchaseItems.product', 'purchaseItems.receiveItems', 'warehouse', 'supplier'])
            ->latest('date')
            ->where('supplier_id', $supplier->id);

        if (!empty($purchaseSearch)) {
            // Handle 8-digit formatted purchase number (remove leading zeros)
            $numericPurchaseSearch = ltrim($purchaseSearch, '0');
            if ($numericPurchaseSearch === '') {
                $numericPurchaseSearch = '0';
            }

            $purchaseQuery->where(function ($query) use ($purchaseSearch, $numericPurchaseSearch) {
                $query->where('purchase_number', 'like', '%' . $purchaseSearch . '%')
                    ->orWhere('purchase_number', $numericPurchaseSearch) // Strict match for stripped ID
                    ->orWhere('purchase_number', 'like', '%' . $numericPurchaseSearch . '%') // Partial match for stripped ID
                    ->orWhere('status', 'like', '%' . $purchaseSearch . '%')
                    ->orWhere('total', 'like', '%' . $purchaseSearch . '%');
            });
        }

        // Get paginated purchases (preserving product_page and product_search in pagination)
        $purchases = $purchaseQuery->paginate(10, ['*'], 'purchase_page', $purchasePage)
            ->appends([
                'tab' => 'purchase',
                'product_search' => $productSearch,
                'product_page' => $productPage,
                'purchase_search' => $purchaseSearch,
                'payment_page' => $paymentPage
            ]);

        // Get products with pagination
        $productsData = $this->getProductsWithPagination($supplier->id, $productSearch, $productPage, $purchaseSearch, $purchasePage);

        // Get payments with pagination
        $paymentsQuery = \App\Models\PurchasePayment::query()
            ->with(['purchase', 'createdBy'])
            ->whereHas('purchase', function ($q) use ($supplier) {
                $q->where('supplier_id', $supplier->id);
            })
            ->latest('date');

        if (!empty($paymentSearch)) {
            $paymentsQuery->where(function ($query) use ($paymentSearch) {
                $query->where('payment_type', 'like', '%' . $paymentSearch . '%')
                    ->orWhere('amount', 'like', '%' . $paymentSearch . '%')
                    ->orWhere('notes', 'like', '%' . $paymentSearch . '%')
                    ->orWhereHas('purchase', function ($q) use ($paymentSearch) {
                        // Handle 8-digit formatted purchase number (remove leading zeros)
                        $numericSearch = ltrim($paymentSearch, '0');
                        if ($numericSearch === '') {
                            $numericSearch = '0';
                        }
                        $q->where('purchase_number', 'like', '%' . $paymentSearch . '%')
                            ->orWhere('purchase_number', $numericSearch)
                            ->orWhere('purchase_number', 'like', '%' . $numericSearch . '%');
                    })
                    ->orWhereHas('createdBy', function ($q) use ($paymentSearch) {
                        $q->where('name', 'like', '%' . $paymentSearch . '%');
                    });
            });
        }

        $payments = $paymentsQuery->paginate(10, ['*'], 'payment_page', $paymentPage)
            ->appends([
                'tab' => 'payment',
                'product_search' => $productSearch,
                'product_page' => $productPage,
                'purchase_search' => $purchaseSearch,
                'purchase_page' => $purchasePage,
                'payment_search' => $paymentSearch
            ]);

        return [
            'supplier' => $supplier,
            'purchases' => $purchases,
            'products' => $productsData['products'],
            'payments' => $payments,
            'purchaseSearch' => $purchaseSearch,
            'productSearch' => $productSearch,
            'paymentSearch' => $paymentSearch,
        ];
    }

    public function getAllSupplierPurchases($supplierId, $search = '')
    {
        $purchaseQuery = Purchase::query()
            ->with(['purchaseItems.product', 'purchaseItems.receiveItems', 'warehouse', 'supplier'])
            ->latest('date')
            ->where('supplier_id', $supplierId);

        if (!empty($search)) {
            // Handle 8-digit formatted purchase number (remove leading zeros)
            $numericSearch = ltrim($search, '0');
            if ($numericSearch === '') {
                $numericSearch = '0';
            }

            $purchaseQuery->where(function ($query) use ($search, $numericSearch) {
                $query->where('purchase_number', 'like', '%' . $search . '%')
                    ->orWhere('purchase_number', $numericSearch) // Strict match for stripped ID
                    ->orWhere('purchase_number', 'like', '%' . $numericSearch . '%') // Partial match for stripped ID
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('total', 'like', '%' . $search . '%');
            });
        }

        return $purchaseQuery->get();
    }

    public function getAllSupplierProducts($supplierId, $search = '')
    {
        // Get all purchase items for this supplier
        $query = \App\Models\PurchaseItem::query()
            ->with('product')
            ->whereHas('purchase', function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            });

        if (!empty($search)) {
            // Handle 8-digit formatted product ID (remove leading zeros)
            $numericSearch = ltrim($search, '0');
            // If the search was all zeros (like "00000000"), keep at least one zero/use original
            if ($numericSearch === '') {
                $numericSearch = '0';
            }

            $query->where(function ($q) use ($search, $numericSearch) {
                // Search by raw product_id or the numeric version (without leading zeros)
                $q->where('product_id', $search)
                    ->orWhere('product_id', $numericSearch)
                    ->orWhere('product_id', 'like', '%' . $numericSearch . '%')
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'like', '%' . $search . '%')
                            ->orWhere('sku', 'like', '%' . $search . '%');
                    });
            });
        }

        // Group by product_id using a subquery and aggregate
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

    public function getProductsWithPagination($supplierId, $search = '', $page = 1, $purchaseSearch = '', $purchasePage = 1)
    {
        $products = $this->getAllSupplierProducts($supplierId, $search);

        // Manual pagination
        $perPage = 10;
        $currentPage = $page;
        $total = $products->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedProducts = $products->slice($offset, $perPage)->values();

        // Create a LengthAwarePaginator
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedProducts,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'product_page',
            ]
        );

        $paginator->appends([
            'tab' => 'product',
            'product_search' => $search,
            'purchase_search' => $purchaseSearch,
            'purchase_page' => $purchasePage,
        ]);

        return [
            'products' => $paginator,
        ];
    }

    public function products($purchases)
    {
        $products = [];
        foreach ($purchases as $purchase){
            foreach ($purchase->purchaseItems as $item){
                $products[] = [

                    'product_id'    => $item->product_id,
                    'variant_id'    => $item->variation_id ?? null, // keep variant_id if exists
                    'product_name'  => $item->product->name,
                    'variant_name'  => $item->variation->name ?? null, // if variant relation exists
                    'sku'           => $item->variation->sku ?? $item->product->sku, // prefer variant sku
                    'price'         => $item->price,
                    'quantity'      => $item->quantity,
                ];
            }
        }

        return $products;
    }
    public function all()
    {
        return $this->model->orderBy('first_name','asc')->paginate(10);
    }
}
