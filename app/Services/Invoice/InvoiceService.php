<?php

namespace App\Services\Invoice;

use PDF;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Platform;
use App\Mail\InvoiceSend;
use App\Models\Warehouse;
use App\Models\SaleReturn;
use App\Models\InvoiceItem;
use Illuminate\Support\Str;
use App\Models\ProductStock;
use App\Services\BaseService;
use App\Models\InvoicePayment;
use App\Models\SaleReturnItems;
use App\Models\SaleReturnRequest;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStockHistory;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\SaleReturnItemRequest;
use App\Services\Payments\PaypalService;
use App\Services\Payments\StripeService;
use App\Services\Product\ProductService;
use App\Traits\ProductStockHistoryTrait;
use App\Notifications\GlobalNotification;
use App\Traits\Woocommerce;

/**
 * InvoiceService
 */
class InvoiceService extends BaseService
{
    use ApiReturnFormatTrait,Woocommerce;
    use ProductStockHistoryTrait;

    /**
     * Rounding slack (in kg) when comparing a sale against available stock.
     * Barrel stock is stored as decimal(18,4), so converting it to kg can lose
     * a sliver; without this the last fraction of a kg becomes unsellable.
     */
    private const STOCK_TOLERANCE_KG = 0.0001;

    protected $productService;
    protected $stripeService;
    protected $paypalService;
    protected $productStock;
    protected $product;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $model = new Invoice();
        parent::__construct($model);

        $this->productService = app(ProductService::class);
        $this->stripeService = app(StripeService::class);
        $this->paypalService = app(PaypalService::class);
        $this->productStock = app(ProductStock::class);
        $this->product = app(Product::class);
    }

    /**
     * getAllStatus
     *
     * @return array
     */
    public function getAllStatus(): array
    {
        try {
            return $this->model::INVOICE_ALL_STATUS;
        } catch (Throwable $th) {
            throw $th;
        }
    }


    /**
     * filterPaymentByDateRange
     *
     * @param  mixed $start
     * @param  mixed $end
     * @param  mixed $with
     * @return void
     */
    public function filterPaymentByDateRange($start = null, $end = null, $with = [])
    {
        try {
            $query = InvoicePayment::query()->with($with);

            if ($start) {
                $query->whereDate('date', '>=', $start);
            }

            if ($end) {
                $query->whereDate('date', '<=', $end);
            }

            return $query->when(request('warehouse'), function ($q) {
                $q->whereHas('invoice.warehouse', function ($q) {
                    $q->where('warehouse_id', request('warehouse'));
                });
            })
                ->when(Auth()->guard('customer')->check(), function ($q) {
                    $q->whereHas('invoice', function ($q) {
                        $q->where('customer_id', user_id());
                    });
                })
                ->when(Auth()->guard('api_customer')->check(), function ($q) {
                    $q->whereHas('invoice', function ($q) {
                        $q->where('customer_id', api_user_id());
                    });
                })
                ->whereNotNull('amount')->orderBy('date', 'DESC')->get();
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function invoiceCustomerEmail($id)
    {
        $invoice = $this->get($id);

        if ($invoice->customer_id) {
            return $invoice->customer['email'];
        } else {
            return '';
        }
    }

    /**
     * getAllPayments
     *
     * @return void
     */
    public function getAllPayments($with = [])
    {
        return InvoicePayment::with($with)
            ->when(request('warehouse'), function ($q) {
                $q->whereHas('invoice.warehouse', function ($q) {
                    $q->where('warehouse_id', request('warehouse'));
                });
            })
            ->when(Auth()->guard('customer')->check(), function ($q) {
                $q->whereHas('invoice', function ($q) {
                    $q->where('customer_id', user_id());
                });
            })
            ->when(Auth()->guard('api_customer')->check(), function ($q) {
                $q->whereHas('invoice', function ($q) {
                    $q->where('customer_id', api_user_id());
                });
            })
            ->whereNotNull('amount')->get();
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

            return $query->when(request('warehouse'), fn($q) => $q->where('warehouse_id', request('warehouse')))
                ->when(Auth()->guard('customer')->check(), fn($q) => $q->where('customer_id', user_id()))
                ->when(Auth()->guard('api_customer')->check(), fn($q) => $q->where('customer_id', api_user_id()))
                ->orderBy('date', 'DESC')
                ->get();
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function filterWareHouseWiseAll($with = [])
    {
        try {
            return $this->model::query()
                ->with($with)
                ->when(request('warehouse'), fn($q) => $q->where('warehouse_id', request('warehouse')))
                ->when(Auth()->guard('customer')->check(), fn($q) => $q->where('customer_id', user_id()))
                ->when(Auth()->guard('api_customer')->check(), fn($q) => $q->where('customer_id', api_user_id()))
                ->orderBy('date', 'DESC')
                ->get();
        } catch (Throwable $th) {
            throw $th;
        }
    }

    /**
     * download
     *
     * @param  mixed $id
     * @return void
     */
    public function download($id)
    {
        try {
            $invoice = $this->get($id, ['items.product.category', 'items.product.carton', 'payments', 'customerInfo']);

            if (auth()->guard('api_customer')->check()) {
                if (!$invoice) {
                    return $this->responseWithError('Invoice not found', [], 404);
                }
                if ($invoice->customer_id != auth()->guard('api_customer')->user()->id) {
                    return $this->responseWithError('You are not authorized to view this invoice', [], 403);
                }
            }
            if (!$invoice) {
                abort(404);
            }

            // Use mPDF (not DomPDF) so Bangla complex-script text shapes correctly.
            // Extra bottom margin leaves room for the bottom-pinned terms footer.
            return render_mpdf(
                'admin.invoices.pdf.invoice',
                ['data' => $invoice],
                'Invoice_' . make8digits($invoice->id) . '.pdf',
                [
                    'margin_top'    => 8,
                    'margin_bottom' => 30,
                    'margin_footer' => 5,
                ]
            );
        } catch (Throwable $th) {
            if (auth()->guard('api_customer')->check()) {
                return $this->responseWithError($th->getMessage(), [], 500);
            }
            throw $th;
        }
    }

    /**
     * storeOrUpdate
     *
     * @param  mixed $data
     * @param  mixed $id
     * @return void
     */
    public function storeOrUpdate(array $data, $id = null)
    {

        try {

            DB::beginTransaction();
            if ($id) {
                $invoice = $this->model::findOrFail($id);
            } else {
                $invoice = new $this->model();
                if (Auth()->guard('customer')->check()) {
                    //                    $invoice->created_by = Auth()->guard('customer')->user()->id;
                } elseif (Auth()->guard('api_customer')->check()) {
                    if ($data['customer_id'] != Auth()->guard('api_customer')->user()->id) {
                        $data['customer_id'] = Auth()->guard('api_customer')->user()->id;
                    }
                    //                    $invoice->created_by = Auth()->guard('api_customer')->user()->id;
                } else {
                    $invoice->created_by = Auth::id();
                }
            }
            $invoice->date = Carbon::parse($data['date'])->format('Y-m-d H:i:s');
            $invoice->due_date = Carbon::parse($data['due_date'])->format('Y-m-d H:i:s');
            $invoice->customer_id = $data['customer_id'];
            $invoice->customer = $data['customer'];
            $invoice->warehouse_id = $data['warehouse_id'];
            $invoice->billing_info = $data['billing'];
            $invoice->shipping_info = $data['shipping'];
            $invoice->items_data = $data['items'];
            $cashAmount    = floatval($data['cash_amount']    ?? 0);
            $bankAmount    = floatval($data['bank_amount']    ?? 0);
            $balanceAmount = floatval($data['balance_amount'] ?? 0);
            $totalPaid     = $cashAmount + $bankAmount + $balanceAmount;

            $invoice->total_paid   = $totalPaid;
            $invoice->last_paid    = $totalPaid;
            $invoice->payment_type = $data['payment_type'] ?? $this->model::PAYMENT_TYPE_CASH;
            if (isset($data['bank_info'])) {
                $invoice->bank_info = $data['bank_info'] ?? null;
            }
            $invoice->global_discount = $data['discount'];
            $invoice->global_discount_type = $data['discount_type'];
            $invoice->additional_charge_name = $data['additional_charge_name'] ?? null;
            $invoice->additional_charge_amount = $data['additional_charge_amount'] ?? 0;
            $invoice->notes = $data['notes'];
            $invoice->status = $this->model::STATUS_PENDING;
            $invoice->token = Str::random(64);
            $invoice->updated_by = Auth::id();
            if (Auth()->guard('customer')->check() || Auth()->guard('api_customer')->check()) {
                $invoice->delivery_status               = $this->model::DELIVERY_STATUS_PENDING;
                $invoice->invoice_created_from          = $this->model::CREATED_FROM_CUSTOMER;
            } else {

                if (isset($data['is_delivered']) && $data['is_delivered'] != null && $data['is_delivered'] == true) {
                    $invoice->delivery_status               = $this->model::DELIVERY_STATUS_DELIVERED;
                } else {
                    $invoice->delivery_status               = $this->model::DELIVERY_STATUS_PENDING;
                }
            }
            $invoice->save();

            $gross_total = 0;
            $total_tax = 0;
            $total_discount = 0;

            // Admin web only: a sold-by-weight line that arrives with no `unit`
            // key means the page is running pre-kg JavaScript (stale tab/cache).
            // Storing it would silently treat kg as barrels, so reject instead.
            $isAdminWeb = !auth()->guard('customer')->check()
                && !auth()->guard('api_customer')->check()
                && !auth()->guard('api')->check();

            // Capture existing free items before deletion so we can restore their stock on update
            $existingFreeItems = $id
                ? InvoiceItem::where('invoice_id', $id)->where('is_free', true)->get()
                : collect();

            if (request()->method() == "PUT") {
                $this->stockPlusUpdate($data['items'], $id);
                // Restore stock for free items being replaced
                foreach ($existingFreeItems as $freeItem) {
                    $warehouseId  = $data['warehouse_id'] ?? $invoice->warehouse_id;
                    $freeStockRow = \App\Models\ProductStock::where('product_id', $freeItem->product_id)
                        ->where('warehouse_id', $warehouseId)
                        ->first();
                    if ($freeStockRow) {
                        $freeStockRow->increment('quantity', $freeItem->quantity);
                    }
                }
            }
            // Delete old item
            InvoiceItem::where('invoice_id', $id)->delete();

            // Add new item
            if ($data['items']) {
                foreach ($data['items'] as $item) {
                    // dd($item);
                    $product                    = $this->productService->get($item['product_id']);

                    if (!$product) continue;

                    if (!$id && $isAdminWeb && $product->is_weight_based && !array_key_exists('unit', $item)) {
                        abort(422, __('custom.stale_page_refresh'));
                    }

                    $unit = $item['unit'] ?? 'barrel';

                    // Effective per-unit price used for all money math. For kg
                    // sales we derive it from the (un-rounded) per-barrel price so
                    // line totals are penny-exact for any conversion factor:
                    // per-kg = barrel_price / kg_per_barrel.
                    $effPrice = (float) $item['price'];
                    if ($unit === 'kg' && $product->is_weight_based && (float) $product->kg_per_barrel > 0) {
                        $barrelPrice = isset($item['price_per_barrel'])
                            ? (float) $item['price_per_barrel']
                            : ((float) $item['price'] * (float) $product->kg_per_barrel);
                        $effPrice = $barrelPrice / (float) $product->kg_per_barrel;
                    }

                    $i_item                     = new InvoiceItem();
                    $i_item->invoice_id         = $invoice->id;
                    $i_item->product_id         = $item['product_id'];
                    $i_item->product_stock_id   = $item['id'];
                    $i_item->product_name       = $product->name;
                    $i_item->sku                = $product->sku;
                    $i_item->batch                = $item['batch'];
                    $i_item->quantity           = $item['quantity'];
                    $i_item->unit               = $unit;
                    $i_item->price              = round($effPrice, 2);
                    $i_item->discount           = $item['discount'];
                    $i_item->discount_type      = $item['discount_type'];
                    $i_item->sub_total          = $effPrice * $item['quantity'];

                    // Tax percent
                    if ($product->tax_status == Product::TAX_INCLUDED) {
                        if ($product->custom_tax) {
                            $i_item->tax        = $product->custom_tax;
                        } else {
                            $i_item->tax        = getDefaultTax();
                        }
                    }

                    // Calculate discount
                    $discount_amount = 0;
                    if ($item['discount_type'] == $this->model::DISCOUNT_PERCENT) {
                        $discount_amount        = $effPrice * ($item['discount'] / 100);
                    } else {
                        $discount_amount        = $item['discount'];
                    }
                    $i_item->sub_total          = round(($effPrice - $discount_amount) * $item['quantity'], 2);
                    $i_item->save();

                    $gross_total                += $i_item->sub_total;

                    // Calculate discount
                    $total_discount             += $discount_amount * $item['quantity'];

                    // Calculate tax
                    $tax_amount                 = $effPrice * ($i_item->tax / 100);
                    $total_tax                  += $tax_amount * $i_item->quantity;
                }
            }


            // Server-side stock guard (POST and PUT). On PUT the old stock has
            // already been restored above, so the check sees true availability.
            // The kg-only rule is enforced where the form hides the barrel
            // option: new admin-web invoices. Existing lines being edited and
            // API clients keep their unit and are only checked for over-sell.
            $this->validateStockAvailability($data['items'], !$id && $isAdminWeb);

            $this->stockUpdate($data['items'], $invoice->id);

            // If update delete old paymets
            if ($id) {
                $oldPayments = InvoicePayment::where('invoice_id', $id)->get();
                foreach($oldPayments as $oP) {
                    if (in_array($oP->payment_type, ['bank', 'online', 'cash']) && floatval($oP->amount) > 0) {
                        if ($oP->payment_type == 'cash') {
                            // For cash payments, find cash account
                            $account = \App\Models\Account::where('type', 'cash')->where('is_active', true)->first();
                        } else {
                            // For bank/online payments, get account from bank_info
                            $bank_info = is_string($oP->bank_info) ? json_decode($oP->bank_info, true) : $oP->bank_info;
                            $accountId = $bank_info['bank_name'] ?? null;
                            $account = $accountId ? \App\Models\Account::find($accountId) : null;
                        }

                        if ($account) {
                            // Find the specific transaction added for this invoice payment
                            $transaction = \App\Models\Transaction::where('account_id', $account->id)
                                ->where('reference_id', $invoice->id)
                                ->where('reference_type', 'invoice')
                                ->first();
                            if ($transaction) {
                                $account->decrement('current_balance', $transaction->amount);
                                $transaction->delete();
                            }
                        }
                    }

                    // Give back balance that the payment being replaced had used,
                    // so the re-created payment below deducts it exactly once.
                    // (payment_type reads back upper-cased, hence the strtolower.)
                    if (strtolower($oP->payment_type) === $this->model::PAYMENT_TYPE_BALANCE && floatval($oP->amount) > 0 && $oP->customer_id) {
                        \App\Models\Customer::find($oP->customer_id)
                            ?->increment('opening_balance', $oP->amount);
                    }
                }
                InvoicePayment::where('invoice_id', $id)->delete();
            }

            // ── Invoice payments (combined: cash + bank + balance) ────────
            $paymentDate = Carbon::parse($data['date'])->format('Y-m-d H:i:s');
            $invoiceRef  = $invoice->token ?? $invoice->id;

            if ($cashAmount > 0) {
                InvoicePayment::create([
                    'invoice_id'   => $invoice->id,
                    'customer_id'  => $invoice->customer_id,
                    'date'         => $paymentDate,
                    'payment_type' => $this->model::PAYMENT_TYPE_CASH,
                    'amount'       => $cashAmount,
                    'created_by'   => Auth::id(),
                ]);
                $cashAccount = \App\Models\Account::where('type', 'cash')->where('is_active', true)->first();
                if ($cashAccount) {
                    $cashAccount->recordInvoicePayment($cashAmount, $invoice->id, 'Cash payment: ' . $invoiceRef);
                }
            }

            if ($bankAmount > 0) {
                InvoicePayment::create([
                    'invoice_id'   => $invoice->id,
                    'customer_id'  => $invoice->customer_id,
                    'date'         => $paymentDate,
                    'payment_type' => $this->model::PAYMENT_TYPE_BANK,
                    'amount'       => $bankAmount,
                    'bank_info'    => $invoice->bank_info ?? [],
                    'created_by'   => Auth::id(),
                ]);
                $accountId = $data['bank_info']['bank_name'] ?? null;
                if ($accountId) {
                    $bankAcc = \App\Models\Account::find($accountId);
                    if ($bankAcc) {
                        $bankAcc->recordInvoicePayment($bankAmount, $invoice->id, 'Bank payment: ' . $invoiceRef);
                    }
                }
            }

            if ($balanceAmount > 0) {
                InvoicePayment::create([
                    'invoice_id'   => $invoice->id,
                    'customer_id'  => $invoice->customer_id,
                    'date'         => $paymentDate,
                    'payment_type' => $this->model::PAYMENT_TYPE_BALANCE,
                    'amount'       => $balanceAmount,
                    'created_by'   => Auth::id(),
                ]);

                // The balance only moves for the amount explicitly applied here,
                // and always alongside the payment row that records it.
                if ($invoice->customer_id) {
                    \App\Models\Customer::find($invoice->customer_id)
                        ?->decrement('opening_balance', $balanceAmount);
                }
            }

            $total = $gross_total;

            // Calculate global discount
            $global_discount_amount = 0;
            if ($data['discount_type'] == $this->model::DISCOUNT_PERCENT) {
                $global_discount_amount = $total * ($data['discount'] / 100);
            } else {
                $global_discount_amount = $data['discount'];
            }

            $total -= $global_discount_amount;
            $total_discount += $global_discount_amount;

            // Update invoice info
            $invoice->tax_amount = $total_tax;
            $invoice->discount_amount = $total_discount;
            $invoice->total = $total + $total_tax + $invoice->additional_charge_amount;

            // Update status based on combined total paid
            if ($totalPaid > 0) {
                $invoiceFinalTotal = $total + $total_tax + $invoice->additional_charge_amount;
                if ($totalPaid >= $invoiceFinalTotal) {
                    $invoice->status = $this->model::STATUS_PAID;
                } else {
                    $invoice->status = $this->model::STATUS_PARTIALLY_PAID;
                }
            }
            if ((auth()->guard('customer')->check() || auth()->guard('api_customer')->check()) && $invoice->payment_type  == 'online') {
                $invoice->status        = $this->model::STATUS_PENDING;
                $invoice->last_paid     = $total + $total_tax;
                $invoice->payment_type  = 'online';
            }

            $invoice->save();

            // ── Free items ────────────────────────────────────────────────
            if (!empty($data['free_items'])) {
                $freeNoteParts = [];

                foreach ($data['free_items'] as $freeItem) {
                    $freeProductId  = $freeItem['product_id'] ?? null;
                    $freeQty        = (int) ($freeItem['quantity'] ?? 0);
                    if (!$freeProductId || $freeQty < 1) continue;

                    $freeProduct = $this->productService->get($freeProductId);
                    if (!$freeProduct) continue;

                    // Save as invoice item with is_free = true, price = 0
                    $freeInvoiceItem = new InvoiceItem();
                    $freeInvoiceItem->invoice_id      = $invoice->id;
                    $freeInvoiceItem->product_id      = $freeProduct->id;
                    $freeInvoiceItem->product_stock_id = $freeItem['product_stock_id'] ?? null;
                    $freeInvoiceItem->product_name    = $freeProduct->name;
                    $freeInvoiceItem->sku             = $freeProduct->sku;
                    $freeInvoiceItem->quantity        = $freeQty;
                    $freeInvoiceItem->price           = 0;
                    $freeInvoiceItem->sub_total       = 0;
                    $freeInvoiceItem->discount        = 0;
                    $freeInvoiceItem->discount_type   = 'percent';
                    $freeInvoiceItem->is_free         = true;
                    $freeInvoiceItem->save();

                    // Deduct stock (same warehouse)
                    $warehouseId = $data['warehouse_id'] ?? $invoice->warehouse_id;
                    $freeStockRow = ProductStock::where('product_id', $freeProduct->id)
                        ->where('warehouse_id', $warehouseId)
                        ->first();

                    if ($freeStockRow) {
                        $freeStockRow->update(['quantity' => max(0, $freeStockRow->quantity - $freeQty)]);
                        $this->productStockHistoryCreate(
                            $freeStockRow->id,
                            $freeStockRow->warehouse_id,
                            $freeStockRow->product_id,
                            Invoice::class,
                            $invoice->id,
                            $freeQty,
                            ProductStockHistory::TYPE_OUT,
                            ProductStockHistory::ACTION_FROM_FREE_ITEM,
                            true,
                            'Free item — Invoice #' . ($invoice->token ?? $invoice->id)
                        );
                    }

                    $freeProduct->decrement('stock', $freeQty);
                    $freeNoteParts[] = $freeQty . '× ' . $freeProduct->name;
                }

                // Auto-append to invoice notes
                if (!empty($freeNoteParts)) {
                    $freeNote = 'Free items: ' . implode(', ', $freeNoteParts);
                    $invoice->notes = trim(($invoice->notes ?? '') . "\n" . $freeNote);
                    $invoice->save();
                }
            }

            // The customer's balance is only touched where it is applied as a
            // balance payment (see the balance payment block above). An unpaid
            // invoice stays a due — it is never silently taken out of the balance.

            DB::commit();
            return $invoice;
        } catch (Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function stockCheck($stock_id, $quantity, $unit = 'barrel')
    {
        $stock = ProductStock::find($stock_id);


        if ($stock == null) {
            abort(422, 'Stock not available');
        }
        // Compare like-for-like: sold-by-weight lines are measured in kg so a
        // remainder below one barrel is still sellable.
        if (!$this->hasEnoughStock($stock, $unit, $quantity)) {
            if (auth()->guard('api_customer')->check() || auth()->guard('api')->check()) {
                return false;
            } else {
                abort(422, 'Stock not available');
            }
        }
        return true;
    }

    /**
     * addPayment
     *
     * @param  mixed $data
     * @return void
     */
    public function addPayment(array $data)
    {
        try {
            $invoice = $this->get($data['invoice_id']);

            $payment = new InvoicePayment();
            $payment->fill([
                'invoice_id' => $invoice->id,
                'date' => $data['date'],
                'payment_type' => $data['payment_type'],
                'amount' => $data['amount'],
                'notes' => $data['notes'],
                'created_by' => Auth::id(),
                'bank_info' => $data['bank_info'] ?? []
            ]);

            if ($payment->save()) {
                $invoice->total_paid = $invoice->total_paid + $payment->amount;
                $invoice->save();

                if ($payment->amount > 0) {
                    if ($payment->payment_type == 'CASH' || strtoupper($payment->payment_type) == 'CASH') {
                        // For cash payments, find cash account and add balance
                        $cashAccount = \App\Models\Account::where('type', 'cash')->where('is_active', true)->first();
                        if ($cashAccount) {
                            $cashAccount->recordInvoicePayment((float)$payment->amount, $invoice->id, 'Invoice Payment: ' . ($invoice->invoice_no ?? $invoice->id));
                        }
                    } elseif ($payment->payment_type == 'BANK' || $payment->payment_type == 'ONLINE' || strtoupper($payment->payment_type) == 'BANK' || strtoupper($payment->payment_type) == 'ONLINE') {
                        $bank_info = $data['bank_info'] ?? null;
                        $accountId = $bank_info['bank_name'] ?? null;
                        if ($accountId) {
                            $account = \App\Models\Account::find($accountId);
                            if ($account) {
                                $account->recordInvoicePayment((float)$payment->amount, $invoice->id, 'Invoice Payment: ' . ($invoice->invoice_no ?? $invoice->id));
                            }
                        }
                    }
                }
            }

            return $payment;
        } catch (Throwable $th) {
            throw $th;
        }
    }

    /**
     * getPayments
     *
     * @param  mixed $invoice_id
     * @return void
     */
    public function getPaymentTypeAttribute($value): string
    {
        return strtoupper($value ?? '');
    }
    public function getPayments($invoice_id)
    {
        try {
            return InvoicePayment::where('invoice_id', $invoice_id)->orderBy('id', 'DESC')->get();
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function deleteInvoice($id)
    {
        try {
            DB::beginTransaction();
            $invoice            = Invoice::where('id', $id)->with('items')->first();
            $invoice_items      = $invoice->items;

            $invoice_sale_return_request    = SaleReturnRequest::where('invoice_id', $id)->first();
            $sale_return                    = SaleReturn::where('invoice_id', $id)->first();
            foreach ($invoice_items as $invoice_item) {
                SaleReturnItemRequest::where('invoice_item_id', $invoice_item->id)->delete();
                SaleReturnItems::where('invoice_item_id', $invoice_item->id)->delete();
            }
            if ($invoice_sale_return_request) {
                $invoice_sale_return_request->delete();
            }
            if ($sale_return) {
                $sale_return->delete();
            }

            // Return the consumed stock before removing the line items.
            $this->restoreInvoiceStock($invoice_items, $id);

            // Explicitly delete invoice items and payments
            InvoiceItem::where('invoice_id', $id)->delete();
            InvoicePayment::where('invoice_id', $id)->delete();

            if ($invoice->delete()) {
                DB::commit();
                flash(__('custom.invoice_deleted_successful'))->success();
                return true;
            } else {
                DB::rollBack();
                flash(__('custom.invoice_deleted_failed'))->error();
                return false;
            }
        } catch (Throwable $th) {
            DB::rollBack();
            flash(__('custom.invoice_deleted_failed'))->error();
            return false;
        }
    }
    public function deleteInvoiceByAPI($id)
    {
        try {
            DB::beginTransaction();
            $invoice            = Invoice::where('id', $id)->with('items')->first();
            $invoice_items      = $invoice->items;

            $invoice_sale_return_request    = SaleReturnRequest::where('invoice_id', $id)->first();
            $sale_return                    = SaleReturn::where('invoice_id', $id)->first();
            foreach ($invoice_items as $invoice_item) {
                SaleReturnItemRequest::where('invoice_item_id', $invoice_item->id)->delete();
                SaleReturnItems::where('invoice_item_id', $invoice_item->id)->delete();
            }
            if ($invoice_sale_return_request) {
                $invoice_sale_return_request->delete();
            }
            if ($sale_return) {
                $sale_return->delete();
            }

            // Return the consumed stock before removing the line items.
            $this->restoreInvoiceStock($invoice_items, $id);

            // Explicitly delete invoice items and payments
            InvoiceItem::where('invoice_id', $id)->delete();
            InvoicePayment::where('invoice_id', $id)->delete();

            if ($invoice->delete()) {
                DB::commit();
                return true;
            } else {
                DB::rollBack();
                return false;
            }
        } catch (Throwable $th) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * deletePayment
     *
     * @param  mixed $id
     * @return void
     */
    public function deletePayment($id)
    {
        try {
            $payment = InvoicePayment::findOrFail($id);
            
            if ($payment->invoice_id) {
                // Get the invoice
                $invoice = $this->get($payment->invoice_id);
                // Adjust paid amount
                $invoice->total_paid = $invoice->total_paid - $payment->amount;

                $invoice->save();

                if ($invoice->total > $invoice->total_paid && $invoice->total_paid > 0) {
                    $invoice->status = $this->model::STATUS_PARTIALLY_PAID;
                } elseif ($invoice->total_paid == 0) {
                    $invoice->status = $this->model::STATUS_PENDING;
                } elseif ($invoice->total_paid >= $invoice->total) {
                    $invoice->status = $this->model::STATUS_PAID;
                }
                $invoice->save();

                // Undoing a balance payment returns that credit to the customer.
                // (payment_type reads back upper-cased, hence the strtolower.)
                if (strtolower($payment->payment_type) === $this->model::PAYMENT_TYPE_BALANCE && $payment->customer_id) {
                    \App\Models\Customer::find($payment->customer_id)
                        ?->increment('opening_balance', $payment->amount);
                }
            } else if ($payment->customer_id) {
                // A payment tied to no invoice was money credited to the customer,
                // so removing it takes that credit back off.
                $customer = \App\Models\Customer::find($payment->customer_id);
                if ($customer) {
                    $customer->opening_balance -= $payment->amount;
                    $customer->save();
                }
            }

            // Reverse transaction from account
            if ($payment->amount > 0) {
                // Always give the money back to the account that received it. That
                // account is recorded on the payment itself, so it is used first —
                // the payment_type label may disagree with it (e.g. a payment typed
                // "cash" that was actually taken into a bank account) and reversing
                // by the label would hit an account that never got the money.
                $bank_info = is_string($payment->bank_info) ? json_decode($payment->bank_info, true) : $payment->bank_info;
                $accountId = $bank_info['bank_name'] ?? null;
                $account   = $accountId ? \App\Models\Account::find($accountId) : null;

                if (!$account && strtoupper($payment->payment_type) == 'CASH') {
                    // Older cash payments carry no account reference.
                    $account = \App\Models\Account::where('type', \App\Models\Account::TYPE_CASH)
                        ->where('is_active', true)->first();
                }

                if ($account) {
                    // Find the transaction booked for this payment. Matching the
                    // amount as well keeps a partial payment from reversing the
                    // transaction that belongs to another payment on the same invoice.
                    $query = \App\Models\Transaction::where('account_id', $account->id)
                        ->where('reference_type', 'invoice')
                        ->where('amount', $payment->amount)
                        ->orderBy('id', 'desc');

                    if ($payment->invoice_id) {
                        $query->where('reference_id', $payment->invoice_id);
                    } else {
                        $query->whereNull('reference_id')
                              ->where('note', 'like', '%Customer Bulk Payment%');
                    }

                    $transaction = $query->first();

                    if ($transaction) {
                        $account->decrement('current_balance', $transaction->amount);
                        $transaction->delete();
                    }
                }
            }

            return $payment->delete();
        } catch (Throwable $th) {
            throw $th;
        }
    }

    /**
     * sendInvoice
     *
     * @param  mixed $data
     * @return void
     */
    public function sendInvoice(array $data)
    {
        try {
            $invoice = $this->get($data['invoice_id']);

            return Mail::to($data['email'])->send(new InvoiceSend($invoice));
        } catch (Throwable $th) {
            throw $th;
        }
    }

    /**
     * getByToken
     *
     * @param  mixed $token
     * @return void
     */
    public function getByToken($token)
    {
        try {
            return $this->model::where('token', $token)->first();
        } catch (Throwable $th) {
            throw $th;
        }
    }

    /**
     * generateInvoiceNo
     *
     * @return void
     */
    public function generateInvoiceNo()
    {
        // Generate invoice no
        $invoice_no = 1;
        $last_sale = $this->model::latest()->first();
        if ($last_sale) $invoice_no = $last_sale->id + 1;
        return sprintf("%08d", $invoice_no);
    }


    /**
     * payByStripe
     *
     * @param  mixed $invoice_id
     * @return void
     */
    public function payByStripe($invoice_id)
    {
        $invoice = $this->get($invoice_id);
        if (!$invoice) abort(404);

        return $this->stripeService->pay($invoice);
    }

    /**
     * stripePaymentSuccess
     *
     * @param  mixed $invoice_id
     * @return void
     */
    public function stripePaymentSuccess($invoice_id)
    {
        $invoice = $this->get($invoice_id);
        if (!$invoice) abort(404);

        $invoice->total_paid = $invoice->total_paid + $invoice->last_paid;
        $invoice->payment_type = $this->model::PAYMENT_TYPE_STRIPE;

        if ($invoice->total_paid >= $invoice->total) {
            $invoice->status = $this->model::STATUS_PAID;
        } else {
            $invoice->status = $this->model::STATUS_PARTIALLY_PAID;
        }
        $invoice->save();

        // Store payment
        $payment = new InvoicePayment();
        $payment->fill([
            'invoice_id' => $invoice->id,
            'date' => Carbon::parse(Carbon::now())->format('Y-m-d H:i:s'),
            'payment_type' => $invoice->payment_type,
            'amount' => $invoice->last_paid,
            'created_by' => Auth::id(),
        ]);
        $payment->save();

        if (user_type() == 'customer') {
            $this->paymentNotification($invoice);
        }


        return true;
    }

    /**
     * paypalPaymentSuccess
     *
     * @param  mixed $invoice_id
     * @param  mixed $order_id
     * @return void
     */
    public function paypalPaymentSuccess($invoice_id, $order_id)
    {

        $transaction_amount = $this->paypalService->getTransaction($order_id);
        if (!$transaction_amount) abort(404);


        $invoice = $this->get($invoice_id);
        if (!$invoice) abort(404);

        $invoice->total_paid = $invoice->total_paid + $transaction_amount;

        if ($invoice->total_paid >= $invoice->total) {
            $invoice->status = $this->model::STATUS_PAID;
        } else {
            $invoice->status = $this->model::STATUS_PARTIALLY_PAID;
        }

        $invoice->payment_type = $this->model::PAYMENT_TYPE_PAYPAL;
        $invoice->save();

        // Store payment
        $payment = new InvoicePayment();
        $payment->fill([
            'invoice_id' => $invoice->id,
            'date' => Carbon::parse(Carbon::now())->format('Y-m-d H:i:s'),
            'payment_type' => $invoice->payment_type,
            'amount' => $transaction_amount,
            'created_by' => Auth::id()
        ]);
        $payment->save();

        if (user_type() == 'customer') {
            $this->paymentNotification($invoice);
        }

        return true;
    }


    /**
     * makePayment
     *
     * @param  mixed $invoice_id
     * @param  mixed $data
     * @return void
     */
    public function makePayment($invoice_id, $data)
    {
        $invoice = $this->get($invoice_id);
        if (!$invoice) abort(404);

        $cashAmount    = floatval($data['cash_amount']    ?? 0);
        $bankAmount    = floatval($data['bank_amount']    ?? 0);
        $balanceAmount = floatval($data['balance_amount'] ?? 0);
        $totalPaid     = $cashAmount + $bankAmount + $balanceAmount;

        if ($totalPaid <= 0) return false;

        $paymentRef  = $invoice->invoice_no ?? $invoice->id;
        $paymentDate = Carbon::now()->format('Y-m-d H:i:s');

        // Determine combined payment_type label
        $types = array_filter([
            $cashAmount    > 0 ? $this->model::PAYMENT_TYPE_CASH    : null,
            $bankAmount    > 0 ? $this->model::PAYMENT_TYPE_BANK    : null,
            $balanceAmount > 0 ? $this->model::PAYMENT_TYPE_BALANCE : null,
        ]);
        $paymentType = count($types) === 1
            ? reset($types)
            : $this->model::PAYMENT_TYPE_COMBINED;

        $invoice->last_paid    = $totalPaid;
        $invoice->payment_type = $paymentType;
        $invoice->total_paid   = $invoice->total_paid + $totalPaid;
        if (isset($data['bank_info'])) {
            $invoice->bank_info = $data['bank_info'];
        }

        if ($invoice->total_paid >= $invoice->total) {
            $invoice->status = $this->model::STATUS_PAID;
        } else {
            $invoice->status = $this->model::STATUS_PARTIALLY_PAID;
        }
        $invoice->save();

        // ── Cash payment record ───────────────────────────────────────────
        if ($cashAmount > 0) {
            InvoicePayment::create([
                'invoice_id'   => $invoice->id,
                'customer_id'  => $invoice->customer_id,
                'date'         => $paymentDate,
                'payment_type' => $this->model::PAYMENT_TYPE_CASH,
                'amount'       => $cashAmount,
                'created_by'   => Auth::id(),
            ]);
            $cashAccount = \App\Models\Account::where('type', 'cash')->where('is_active', true)->first();
            if ($cashAccount) {
                $cashAccount->recordInvoicePayment($cashAmount, $invoice->id, 'Cash Payment: ' . $paymentRef);
            }
        }

        // ── Bank payment record ───────────────────────────────────────────
        if ($bankAmount > 0) {
            $bankInfo  = $data['bank_info'] ?? [];
            $accountId = $bankInfo['bank_name'] ?? null;
            InvoicePayment::create([
                'invoice_id'   => $invoice->id,
                'customer_id'  => $invoice->customer_id,
                'date'         => $paymentDate,
                'payment_type' => $this->model::PAYMENT_TYPE_BANK,
                'amount'       => $bankAmount,
                'bank_info'    => $bankInfo,
                'created_by'   => Auth::id(),
            ]);
            if ($accountId) {
                $bankAcc = \App\Models\Account::find($accountId);
                if ($bankAcc) {
                    $bankAcc->recordInvoicePayment($bankAmount, $invoice->id, 'Bank Payment: ' . $paymentRef);
                }
            }
        }

        // ── Balance payment record + deduct from customer ─────────────────
        if ($balanceAmount > 0) {
            InvoicePayment::create([
                'invoice_id'   => $invoice->id,
                'customer_id'  => $invoice->customer_id,
                'date'         => $paymentDate,
                'payment_type' => $this->model::PAYMENT_TYPE_BALANCE,
                'amount'       => $balanceAmount,
                'created_by'   => Auth::id(),
            ]);
            if ($invoice->customer_id) {
                $customer = \App\Models\Customer::find($invoice->customer_id);
                if ($customer) {
                    $customer->decrement('opening_balance', $balanceAmount);
                }
            }
        }

        if (user_type() == 'customer') {
            $this->paymentNotification($invoice);
        }
        return true;
    }

    /**
     * stockUpdate
     *
     * @param  mixed $items
     * @return void
     */

    private function stockUpdate($items, $id = null)
    {
        if (isset($items)) {
            foreach ($items as $key => $item) {
                if ($item['id']) {
                    $stock = $this->getStock($item['id']);
                    // Normalise the sold quantity to the stock's base unit (barrels)
                    // so kg sales decrement the shared stock pool proportionally.
                    $stockQty = $this->toBarrelQuantity(
                        optional($stock)->product,
                        $item['unit'] ?? 'barrel',
                        $item['quantity']
                    );
                    if ($stock) {
                        $stock->update([
                            'quantity' => $stock->quantity - $stockQty
                        ]);
                        Log::info('Stock Out successfully', ['stock' => $stock]);
                        $this->productStockHistoryCreate(
                            $stock->id,
                            $stock->warehouse_id,
                            $stock->product_id,
                            Invoice::class,
                            $id,
                            $stockQty,
                            ProductStockHistory::TYPE_OUT,
                            ProductStockHistory::ACTION_FROM_INVOICE
                        );
                    }

                    $productStock = $this->product->newQuery()->where('id', $item['product_id'])->first();
                    if ($productStock) {
                        $productStock->update([
                            'stock' => max(0, $productStock->stock - $stockQty)
                        ]);

                        $this->deductSubProductStocks($productStock, $stock?->warehouse_id, $stockQty, $id);
                        $this->deductCartonStock($productStock, $stock?->warehouse_id, $stockQty, $id);
                    }

                    $wooStores = getConnectedWooCommerceStores();
                    if($wooStores->isNotEmpty())
                        {

                            foreach($wooStores as $store)
                            {
                              $store_credential = Platform::find($store->id);

                                $this->updateWooCommerceStoresStockForItem($store->id, $store_credential, $item['id'], $item['quantity']);
                            }
                        }
                }
            }
        }
    }


    private function deductSubProductStocks(Product $parentProduct, ?int $warehouseId, float $soldQty, $invoiceId): void
    {
        foreach ($parentProduct->subProducts as $sub) {
            $deductQty = $soldQty * (int) $sub->pivot->quantity;

            if ($warehouseId) {
                $subStock = ProductStock::where('product_id', $sub->id)
                    ->where('warehouse_id', $warehouseId)
                    ->first();

                if ($subStock) {
                    $subStock->update(['quantity' => max(0, $subStock->quantity - $deductQty)]);
                    $this->productStockHistoryCreate(
                        $subStock->id,
                        $subStock->warehouse_id,
                        $subStock->product_id,
                        Invoice::class,
                        $invoiceId,
                        $deductQty,
                        ProductStockHistory::TYPE_OUT,
                        ProductStockHistory::ACTION_FROM_INVOICE
                    );
                }
            }

            $sub->update(['stock' => max(0, $sub->stock - $deductQty)]);
        }
    }

    private function deductCartonStock(Product $product, ?int $warehouseId, float $soldQty, $invoiceId): void
    {
        $carton = $product->carton;
        if (!$carton || !$carton->carton_product_id || $carton->qty_per_carton < 1) {
            return;
        }

        $cartonsToDeduct = intdiv((int) $soldQty, (int) $carton->qty_per_carton);
        if ($cartonsToDeduct < 1) {
            return;
        }

        $cartonProduct = Product::find($carton->carton_product_id);
        if (!$cartonProduct) {
            return;
        }

        if ($warehouseId) {
            $cartonStock = ProductStock::where('product_id', $cartonProduct->id)
                ->where('warehouse_id', $warehouseId)
                ->first();
            if ($cartonStock) {
                $cartonStock->update(['quantity' => max(0, $cartonStock->quantity - $cartonsToDeduct)]);
                $this->productStockHistoryCreate(
                    $cartonStock->id,
                    $cartonStock->warehouse_id,
                    $cartonStock->product_id,
                    Invoice::class,
                    $invoiceId,
                    $cartonsToDeduct,
                    ProductStockHistory::TYPE_OUT,
                    ProductStockHistory::ACTION_FROM_INVOICE
                );
            }
        }

        $cartonProduct->update(['stock' => max(0, $cartonProduct->stock - $cartonsToDeduct)]);
    }

    private function stockPlusUpdate($items, $id)
    {
        if (isset($items)) {
            foreach ($items as $key => $item) {
                if ($item['id']) {

                    $invoiceItem = InvoiceItem::query()
                        ->where('invoice_id', $id)
                        ->where('product_id', $item['product_id'])
                        ->where('product_stock_id', $item['id'])
                        ->first();

                    $itemTableQuantity = optional($invoiceItem)->quantity ?: 0;

                    $stock = $this->getStock($item['id']);
                    // Restore in the stock's base unit (barrels) so a kg sale gives
                    // back exactly what it consumed.
                    $restoreQty = $this->toBarrelQuantity(
                        optional($stock)->product,
                        optional($invoiceItem)->unit ?? 'barrel',
                        $itemTableQuantity
                    );
                    if ($stock) {
                        $stock->update([
                            'quantity' => $stock->quantity + $restoreQty
                        ]);
                        Log::info('Stock In successfully', ['stock' => $stock]);
                        $this->productStockHistoryCreate(
                            $stock->id,
                            $stock->warehouse_id,
                            $stock->product_id,
                            Invoice::class,
                            $id,
                            $restoreQty,
                            ProductStockHistory::TYPE_IN,
                            ProductStockHistory::ACTION_FROM_INVOICE
                        );
                    }

                    $productStock = $this->product->newQuery()->where('id', $item['product_id'])->first();
                    if ($productStock) {
                        $productStock->update([
                            'stock' => $productStock->stock + $restoreQty
                        ]);
                    }
                }
            }
        }
    }

    /**
     * getStock
     *
     * @param  mixed $product
     * @return void
     */
    /**
     * Convert a sold quantity into the stock's base unit (barrels).
     *
     * Stock for sold-by-weight products is held in barrels. When a line is sold
     * in kg, the stock movement is kg / kg_per_barrel barrels. For every other
     * case the quantity is already in the stock unit and is returned unchanged.
     * The conversion factor is read from the product (never hardcoded).
     */
    public function toBarrelQuantity($product, $unit, $quantity)
    {
        $factor = (float) optional($product)->kg_per_barrel;

        if ($unit === 'kg' && optional($product)->is_weight_based && $factor > 0) {
            return $quantity / $factor;
        }

        return $quantity;
    }

    /**
     * Convert a quantity into kg, the comparison unit for sold-by-weight stock.
     *
     * Stock is stored in barrels, so a barrel quantity is scaled up by
     * kg_per_barrel while a kg quantity is already in the comparison unit.
     * Products that are not sold by weight have no conversion and are returned
     * unchanged. The factor always comes from the product (never hardcoded).
     */
    public function toKgQuantity($product, $unit, $quantity)
    {
        $factor = (float) optional($product)->kg_per_barrel;

        if ($unit !== 'kg' && optional($product)->is_weight_based && $factor > 0) {
            return (float) $quantity * $factor;
        }

        return (float) $quantity;
    }

    /**
     * True when the stock row can cover the requested quantity.
     *
     * Sold-by-weight products are always compared in kg, so a remainder smaller
     * than one full barrel is still sellable. Everything else keeps the plain
     * comparison against the stock unit.
     */
    public function hasEnoughStock($stock, $unit, $quantity): bool
    {
        $product = optional($stock)->product;

        if (optional($product)->is_weight_based && (float) $product->kg_per_barrel > 0) {
            $availableKg = $this->toKgQuantity($product, 'barrel', $stock->quantity);
            $neededKg    = $this->toKgQuantity($product, $unit, $quantity);

            // Barrel stock is stored as decimal(18,4); the tolerance keeps the
            // last fraction of a kg sellable instead of losing it to rounding.
            return $neededKg <= $availableKg + self::STOCK_TOLERANCE_KG;
        }

        return (float) $stock->quantity >= (float) $this->toBarrelQuantity($product, $unit, $quantity);
    }

    /**
     * Less than one full barrel left, so the remainder can only be sold in kg.
     * This is the server-side twin of the form hiding the barrel option.
     */
    public function barrelUnitBlocked($stock, $unit): bool
    {
        $product = optional($stock)->product;

        return $unit !== 'kg'
            && optional($product)->is_weight_based
            && (float) $product->kg_per_barrel > 0
            && (float) $stock->quantity < 1;
    }

    /**
     * Block a sale that exceeds available stock (unless backorders are allowed).
     * Sold-by-weight lines are compared in kg, so a remainder below one barrel
     * stays sellable instead of reading as "out of stock".
     *
     * $restrictPartialBarrel mirrors the create form hiding the barrel option:
     * with less than one full barrel left the line must be sold in kg.
     */
    public function validateStockAvailability(array $items, bool $restrictPartialBarrel = false): void
    {
        foreach ($items as $item) {
            if (empty($item['id'])) {
                continue;
            }

            $stock = $this->getStock($item['id']);
            if (!$stock || $stock->backorders_allowed) {
                continue;
            }

            $unit    = $item['unit'] ?? 'barrel';
            $product = optional($stock)->product;

            if ($restrictPartialBarrel && $this->barrelUnitBlocked($stock, $unit)) {
                abort(422, __('custom.sell_remainder_in_kg', [
                    'product'   => optional($product)->name ?? '',
                    'available' => $this->formatStockQuantity(
                        $this->toKgQuantity($product, 'barrel', $stock->quantity)
                    ) . ' ' . __('custom.kg'),
                ]));
            }

            if (!$this->hasEnoughStock($stock, $unit, $item['quantity'])) {
                $isKg    = $unit === 'kg' && optional($product)->is_weight_based;
                $available = $isKg
                    ? (float) $stock->quantity * (float) $product->kg_per_barrel
                    : (float) $stock->quantity;
                $label = $isKg
                    ? __('custom.kg')
                    : (optional($product)->is_weight_based ? ($product->barrel_label ?: __('custom.barrel')) : '');

                abort(422, __('custom.insufficient_stock_for', [
                    'product'   => optional($product)->name ?? '',
                    'available' => trim($this->formatStockQuantity($available) . ' ' . $label),
                ]));
            }
        }
    }

    /**
     * Trim a stock quantity down to a readable figure for user messages.
     */
    private function formatStockQuantity(float $quantity): string
    {
        return rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.');
    }

    /**
     * Return a deleted invoice's consumed stock to the pool. Unit-aware, so a
     * kg line gives back exactly the barrels it took. Mirrors the restore done
     * on edit (stockPlusUpdate) so deleting a sale fully reverses its stock
     * movement.
     */
    public function restoreInvoiceStock($invoiceItems, $invoiceId): void
    {
        foreach ($invoiceItems as $invoiceItem) {
            $stock   = $invoiceItem->product_stock_id ? $this->getStock($invoiceItem->product_stock_id) : null;
            $product = $this->product->newQuery()->find($invoiceItem->product_id);

            $restoreQty = $this->toBarrelQuantity(
                optional($stock)->product ?? $product,
                $invoiceItem->unit ?? 'barrel',
                $invoiceItem->quantity
            );

            if ($stock) {
                $stock->increment('quantity', $restoreQty);
                $this->productStockHistoryCreate(
                    $stock->id,
                    $stock->warehouse_id,
                    $stock->product_id,
                    Invoice::class,
                    $invoiceId,
                    $restoreQty,
                    ProductStockHistory::TYPE_IN,
                    ProductStockHistory::ACTION_FROM_INVOICE
                );
            }

            if ($product) {
                $product->increment('stock', $restoreQty);
            }
        }
    }

    public function getStock($product_stock_id)
    {
        return $this->productStock->newQuery()->where('id', $product_stock_id)->first();

        //        if (request('warehouse_id')){
        //            $defaultWarehouse = request('warehouse_id');
        //        }else{
        //            $defaultWarehouse = Warehouse::query()->where('is_default', true)->first()->id;
        //        }
        //
        //        return $this->productStock
        //            ->newQuery()
        //            ->where('product_id', $product)
        //            ->where('warehouse_id', $defaultWarehouse)
        //            ->first();
    }
    public function deliveryStatusChange($id, $status)
    {

        $invoice = $this->get($id, 'items.product');
        if (!$invoice) abort(404);

        if ($invoice->delivery_status == $this->model::DELIVERY_STATUS_CANCELED && $status != $this->model::DELIVERY_STATUS_CANCELED) {
            Log::info('Stock Out successfully', ['id' => $id]);
            $this->stockUpdate($this->makeItemObj($invoice->items, $id));
        } elseif ($invoice->delivery_status == $this->model::DELIVERY_STATUS_DELIVERED && $status != $this->model::DELIVERY_STATUS_DELIVERED) {
            Log::info('Stock In successfully', ['id' => $id]);
            $this->stockPlusUpdate($this->makeItemObj($invoice->items), $id);
        }
        $invoice->delivery_status = $status;
        $invoice->save();

        if (user_type() == 'admin') {
            try {
                $customer = Customer::where('id', $invoice->customer_id)->first();
                if (config('is_invoice_notification') == 'yes') {
                    Log::info('Invoice Notification');
                    $data = [
                        'title' => 'New Invoice Status Changed',
                        'message' => 'Your new invoice has been' . $status . 'View the invoice for more information.',
                        'url' => route('admin.invoices.show', $invoice->id),
                    ];
                    $customer->notify(new GlobalNotification($data));
                }

                if (config('mail.mailers.smtp.host') != null && config('mail.mailers.smtp.port') != null && config('mail.mailers.smtp.username') != null && config('mail.mailers.smtp.password') != null) {
                    if (config('is_invoice_email') == 'yes' && $customer->count() > 0) {
                        $invoice = $this->get($invoice->id);
                        Mail::to($customer->email)
                            ->send(new InvoiceSend($invoice));
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error sending notification or email: ' . $e->getMessage());
                flash(__('Failed to send notification or email. Please try again later'))->success();
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
        } else {
            try {
                // $superAdmins = User::role('Super Admin')->get();
                // $superAdmin = $superAdmins->first();
                if (config('is_invoice_notification') == 'yes') {
                    Log::info('Invoice Notification');
                    $data = [
                        'title' => 'New Invoice Status Changed',
                        'message' => 'Your new invoice has been' . $status . 'View the invoice for more information.',
                        'url' => route('admin.invoices.show', $invoice->id),
                    ];
                    $users = User::all();

                    foreach ($users as $user) {

                            $user->notify(new GlobalNotification($data));

                    }

                }

                if (config('mail.mailers.smtp.host') != null && config('mail.mailers.smtp.port') != null && config('mail.mailers.smtp.username') != null && config('mail.mailers.smtp.password') != null) {
                    if (config('is_invoice_email') == 'yes') {
                        $superAdmin = User::first();
                        $ccAdmins = User::where('id', '!=', $superAdmin->id)->pluck('email')->toArray();// Get all Super Admins except the first one
                        $invoice = $this->get($invoice->id);
                        Mail::to($superAdmin->email)
                            ->cc($ccAdmins) // Add other Super Admins in CC
                            ->send(new InvoiceSend($invoice));
                    }
                }
            } catch (\Exception $e) {

                Log::error('Error sending notification or email: ' . $e->getMessage());
                flash(__('Failed to send notification or email. Please try again later'))->success();
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
        }
        return true;
    }
    private function makeItemObj($items)
    {
        $itemObj = [];
        foreach ($items as $key => $item) {
            $itemObj[] = [
                'id'            => $item->product_stock_id,
                'product_id'    => $item->product_id,
                'split_sale'    => optional($item->product)->split_sale,
                'sku'           => $item->sku,
                'name'          => $item->product_name,
                'price'         => $item->price,
                'stock'         => $item->product,
                'quantity'      => $item->quantity,
                'tax_status'    => $item->product,
                'custom_tax'    => $item->tax,
                'discount'      => $item->discount,
                'discount_type' => $item->discount_type,
            ];
        }
        return $itemObj;
    }
    public function getInvoiceList()
    {
        $user_id = auth()->guard('api_customer')->check() ? auth()->guard('api_customer')->user()->id : (auth()->guard('customer')->check() ? auth()->guard('customer')->user()->id : null);
        return Invoice::with('warehouse')
            ->when($user_id, function ($query) use ($user_id) {
                $query->where('customer_id', $user_id);
            })
            ->select('invoices.*')->paginate(10);
    }

    public function paymentNotification($invoice)
    {
        try {
            // $superAdmins = User::role('Super Admin')->get();
            // $superAdmin = $superAdmins->first();
            if (config('is_invoice_notification') == 'yes') {
                Log::info('Invoice Notification');
                $data = [
                    'title' => 'New Invoice Payment',
                    'message' => 'Your new invoice has been payment. View the invoice for more information.',
                    'url' => route('admin.invoices.show', $invoice->id),
                ];
                $users = User::all();

                    foreach ($users as $user) {

                            $user->notify(new GlobalNotification($data));

                    }

            }

            if (config('mail.mailers.smtp.host') != null && config('mail.mailers.smtp.port') != null && config('mail.mailers.smtp.username') != null && config('mail.mailers.smtp.password') != null) {
                if (config('is_invoice_email') == 'yes') {
                    $superAdmin = User::first();
                    // Get all other users, skipping the first user
                    $ccAdmins = User::where('id', '!=', $superAdmin->id)->pluck('email')->toArray();
// Get all Super Admins except the first one
                    $invoice = $this->get($invoice->id);
                    Mail::to($superAdmin->email)
                        ->cc($ccAdmins) // Add other Super Admins in CC
                        ->send(new InvoiceSend($invoice));
                }
            }
        } catch (\Exception $e) {

            Log::error('Error sending notification or email: ' . $e->getMessage());
            flash(__('Failed to send notification or email. Please try again later'))->success();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
