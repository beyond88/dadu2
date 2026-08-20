<?php


namespace App\Services\Purchase;


use Throwable;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Traits\SetModel;
use App\Models\Warehouse;
use App\Models\SystemCountry;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

/**
 * PurchaseServices
 */
class PurchaseServices extends BaseService
{
    use SetModel;

    /**
     * __construct
     *
     * @param  mixed $purchase
     * @return void
     */
    public function __construct(Purchase $purchase)
    {
        $this->model = $purchase;
    }

    /**
     * filterByDateRange
     *
     * @param  mixed $start
     * @param  mixed $end
     * @param  mixed $with
     * @return void
     */
    public function filterByDateRange($start = null, $end = null, $with = [])
    {
        try {
            $query = $this->model::query()->with($with);

            if ($start) {
                $query->whereDate('date', '>=', $start);
            }

            if ($end) {
                $query->whereDate('date', '<=', $end);
            }

            if (request('warehouse')){
                $query->where('warehouse_id', request('warehouse'));
            }

            return $query->orderBy('date', 'DESC')->get();

        } catch (Throwable $th) {
            throw $th;
        }
    }


    public function allTime($with = [])
    {
        try {
            $query = $this->model::query()->with($with);

            if (request('warehouse')){
                $query->where('warehouse_id', request('warehouse'));
            }

            return $query->orderBy('date', 'DESC')->get();

        } catch (Throwable $th) {
            throw $th;
        }
    }

    /**
     * createCredentials
     *
     * @return array
     */
    public function createCredentials(): array
    {
        return [
            'suppliers' => Supplier::query()->where('status',Supplier::STATUS_ACTIVE)->get(['id', 'first_name', 'last_name', 'address_line_1']),
            'warehouses' => Warehouse::query()->where('status',Warehouse::STATUS_ACTIVE)->pluck('name', 'id'),
            'countries' => SystemCountry::query()->get(),
            'purchaseNumber' => $this->generateUniquePurchaseNumber(),
        ];
    }

    /**
     * Generate a unique, human-readable purchase number (e.g. MR000123) to
     * pre-fill and display on the create form. Uniqueness is enforced against
     * the unique purchase_number column.
     */
    public function generateUniquePurchaseNumber(): string
    {
        $seq = (int) ($this->model->newQuery()->max('id') ?? 0) + 1;
        do {
            $number = 'MR' . str_pad((string) $seq, 6, '0', STR_PAD_LEFT);
            $seq++;
        } while ($this->model->newQuery()->where('purchase_number', $number)->exists());

        return $number;
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return bool
     */
    public function store($request): bool
    {
        DB::transaction(function () use ($request) {

            $this->storePurchase($request)
                ->storePurchaseItems($request);

            // Auto-receive the purchase in the same action (no separate manual receive step).
            $this->autoReceive();

            // Cash purchase → record payment as PAID and deduct the single Cash account.
            // Credit (default) → no payment record, full amount stays due (unchanged behaviour).
            if ($request->payment_type === 'cash') {
                $this->recordCashPayment();
            }
        });

        return true;
    }

    /**
     * Auto-receive the just-created purchase for its full ordered quantities by
     * reusing the existing PurchaseReceiveServices (stock update + status → confirmed).
     */
    private function autoReceive(): void
    {
        $purchase = $this->model->fresh(['purchaseItems']);
        if (!$purchase || $purchase->purchaseItems->isEmpty()) {
            return;
        }

        $data = [
            'date'              => $purchase->date,
            'total'             => $purchase->total,
            // Receive into the warehouse chosen on the purchase form. Without this the
            // receive service falls back to the default warehouse and the stock lands
            // somewhere other than the warehouse shown on the purchase.
            'warehouse_id'      => $purchase->warehouse_id,
            'purchase_item_id'  => [],
            'product_id'        => [],
            'product_stock_id'  => [],
            'receive_quantity'  => [],
            'receive_price'     => [],
            'receive_sub_total' => [],
            'batch'             => [],
            'expiry_date'       => [],
        ];

        foreach ($purchase->purchaseItems as $item) {
            $data['purchase_item_id'][]  = $item->id;
            $data['product_id'][]        = $item->product_id;
            $data['product_stock_id'][]  = $item->product_stock_id;
            $data['receive_quantity'][]  = $item->quantity;
            $data['receive_price'][]     = $item->price;
            $data['receive_sub_total'][] = $item->sub_total;
            $data['batch'][]             = null;
            $data['expiry_date'][]       = null;
        }

        $receiveRequest = new \Illuminate\Http\Request();
        $receiveRequest->merge($data);

        app(\App\Services\Purchase\PurchaseReceiveServices::class)->store($receiveRequest, $purchase);
    }

    /**
     * Record a cash purchase as PAID and deduct the single active Cash account.
     * Reuses the same ledger path as the manual purchase-payment flow (Account::reduceBalance).
     */
    private function recordCashPayment(): void
    {
        $purchase = $this->model;

        // Resolve the single active Cash account. The "one cash account" rule is enforced
        // app-side (no DB unique constraint), so fail loudly rather than silently pick one.
        $cashAccounts = \App\Models\Account::where('type', \App\Models\Account::TYPE_CASH)
            ->where('is_active', true)
            ->get();

        if ($cashAccounts->count() > 1) {
            throw new \Exception('Multiple active Cash accounts found — only one is allowed. Cannot record cash purchase.');
        }

        $cashAccount = $cashAccounts->first();
        if (!$cashAccount) {
            throw new \Exception('No active Cash account found — cannot record a cash purchase.');
        }

        $amount = (float) $purchase->total;
        $note   = 'Cash purchase #' . $purchase->purchase_number;

        \App\Models\PurchasePayment::create([
            'purchase_id'  => $purchase->id,
            'account_id'   => $cashAccount->id,
            'date'         => $purchase->date,
            'payment_type' => 'cash',
            'amount'       => $amount,
            'notes'        => $note,
            'created_by'   => auth()->id(),
        ]);

        // Deduct from the Cash account (allow negative per requirement) via the existing ledger method.
        $cashAccount->reduceBalance($amount, $note, $purchase->id, 'purchase', true);
    }

    /**
     * purchaseNumber
     *
     * @param  mixed $supplier_id
     * @return string
     */
    private function purchaseNumber($supplier_id): string
    {
        return $supplier_id . date('ymdHis') . rand(1000, 9999);
    }

    /**
     * storePurchase
     *
     * @param  mixed $request
     * @return PurchaseServices
     */
    private function storePurchase($request): PurchaseServices
    {
        // Prefer the number pre-generated on the form (read-only field); fall back
        // to a freshly generated unique number if it's missing or already taken.
        $submittedNumber = $request->purchase_number ?? null;
        $purchaseNumber = ($submittedNumber && !$this->model->newQuery()->where('purchase_number', $submittedNumber)->exists())
            ? $submittedNumber
            : $this->generateUniquePurchaseNumber();

        $this->model = $this->model
            ->newQuery()
            ->create([
                'purchase_number' => $purchaseNumber,
                'supplier_id'     => $request->supplier,
                'warehouse_id'    => $request->warehouse,
                'company'         => $request->company,
                'date'            => $request->date,
                'notes'           => $request->note,
                'total'           => $request->total,
                'status'          => $this->model::STATUS_REQUESTED,

                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'country'        => $request->country,
                'state'          => $request->state,
                'city'           => $request->city,
                'zipcode'        => $request->zipcode,
                'short_address'  => $request->short_address
            ]);

        return $this;
    }

    /**
     * storePurchaseItems
     *
     * @param  mixed $request
     * @return PurchaseServices
     */
    public function storePurchaseItems($request): PurchaseServices
    {
        if (isset($request->product_id) && is_array($request->product_id)) {
            $products = [];
            foreach ($request->product_id as $key => $product_id) {
                $products[] = [
                    'purchase_id'       => $this->model->id,
                    'product_id'        => $product_id,
                    'product_stock_id'  => $request->product_stock_id[$key],
                    'variation_id'     => $request->variation_id[$key] ?? null,
                    'quantity'          => $request->quantity[$key],
                    'price'             => $request->price[$key],
                    'note'              => $request->product_note[$key],
                    'sub_total'         => $request->sub_total[$key],
                    'created_by'        => auth()->id(),
                    'updated_by'        => auth()->id(),
                ];
            }

            $this->model->purchaseItems()->insert($products);
        }

        return $this;
    }

    /**
     * editCredentials
     *
     * @return array
     */
    public function editCredentials(): array
    {
        return array_merge($this->createCredentials(), [
            'purchase' => $this->model->load(['purchaseItems.product','purchaseItems.variation','purchaseItems.productStock.variation']),
        ]);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @return bool
     */
    public function update($request): bool
    {
        DB::transaction(function () use ($request) {

            $this->purchaseUpdate($request)
                ->updatePurchaseItems($request);
        });

        return true;
    }

    /**
     * purchaseUpdate
     *
     * @param  mixed $request
     * @return void
     */
    private function purchaseUpdate($request)
    {
        $this->model->update([
            'purchase_number' => $this->model->purchase_number,
            'supplier_id'     => $request->supplier,
            'warehouse_id'    => $request->warehouse,
            'company'         => $request->company,
            'date'            => $request->date,
            'notes'           => $request->note,
            'total'           => $request->total,
            'status'          => $this->model::STATUS_REQUESTED,

            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'country'        => $request->country,
            'state'          => $request->state,
            'city'           => $request->city,
            'zipcode'        => $request->zipcode,
            'short_address'  => $request->short_address
        ]);

        return $this;
    }

    /**
     * updatePurchaseItems
     *
     * @param  mixed $request
     * @return PurchaseServices
     */
    private function updatePurchaseItems($request): PurchaseServices
    {
        if (isset($request->product_id) && is_array($request->product_id)) {
            foreach ($request->product_id as $key => $product_id) {
                if ($request->purchase_item_id[$key] != null) {
                    $this->model
                        ->purchaseItems()
                        ->where('id', $request->purchase_item_id[$key])
                        ->update([
                            'product_id' => $product_id,
                            'product_stock_id'  => $request->product_stock_id[$key],
                            'variation_id'      => $request->variation_id[$key] ?? null,
                            'quantity' => $request->quantity[$key],
                            'price' => $request->price[$key],
                            'note' => $request->product_note[$key],
                            'sub_total' => $request->sub_total[$key],
                        ]);
                } else {
                    $this->model
                        ->purchaseItems()
                        ->create([
                            'product_id' => $product_id,
                            'product_stock_id'  => $request->product_stock_id[$key],
                            'variation_id'      => $request->variation_id[$key] ?? null,
                            'quantity' => $request->quantity[$key],
                            'price' => $request->price[$key],
                            'note' => $request->product_note[$key],
                            'sub_total' => $request->sub_total[$key],
                        ]);
                }
            }
        }

        return $this;
    }
}
