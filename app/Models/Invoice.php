<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Invoice
 */
class Invoice extends Model
{
    use HasFactory;

    /**
     * casts
     *
     * @var array
     */
        protected $fillable = [
        'token',
        'date',
        'due_date',
        'customer_id',
        'customer',
        'billing_info',
        'shipping_info',
        'items_data',
        'tax_amount',
        'discount_amount',
        'global_discount',
        'global_discount_type',
        'total',
        'total_paid',
        'last_paid',
        'payment_type',
        'notes',
        'status',
        'created_by',
        'warehouse_id',
        'additional_charge_name',
        'additional_charge_amount'

    ];

    protected $casts = [
        'customer' => 'array',
        'billing_info' => 'array',
        'shipping_info' => 'array',
        'items_data' => 'array',
        'bank_info' => 'array'
    ];

    // Status
    public const STATUS_PAID = 'paid';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_CANCELED = 'cancelled';
    public const STATUS_PENDING = 'pending';
    public const CREATED_FROM_ADMIN = 'admin';
    public const CREATED_FROM_CUSTOMER = 'customer';
    public const INVOICE_ALL_STATUS = [
    // WooCommerce Core Statuses
    'pending'    => 'Pending Payment',    // Order received, awaiting payment
    'processing' => 'Processing',         // Payment received, stock reduced, order in progress
    'on-hold'    => 'On Hold',            // Awaiting payment confirmation or manual review
    'completed'  => 'Completed',          // Order fulfilled and completed
    'cancelled'  => 'Cancelled',          // Cancelled by admin or customer
    'refunded'   => 'Refunded',           // Payment refunded
    'failed'     => 'Failed',             // Payment failed or declined
    'trash'      => 'Trashed',            // Moved to trash (not visible to customers)

    // Optional / Common Custom Woo Statuses
    'processing-shipping' => 'Processing - Shipping', // Example custom status
    'delivered' => 'Delivered',                       // Custom: Marked as delivered
    'ready-to-ship' => 'Ready to Ship',               // Custom: Waiting for shipment
    'partially_refunded' => 'Partially Refunded',     // Partial refund issued

    // Your App's Existing Custom Statuses
    'paid'            => 'Paid',
    'partially_paid'  => 'Partially Paid',
    'overdue'         => 'Overdue',
];


    public const DISCOUNT_FIXED = 'fixed';
    public const DISCOUNT_PERCENT = 'percent';

    public const PAYMENT_TYPE_CASH     = 'cash';
    public const PAYMENT_TYPE_ONLINE   = 'online';
    public const PAYMENT_TYPE_PAYPAL   = 'paypal';
    public const PAYMENT_TYPE_STRIPE   = 'stripe';
    public const PAYMENT_TYPE_BANK     = 'bank';
    public const PAYMENT_TYPE_BALANCE  = 'balance';
    public const PAYMENT_TYPE_COMBINED = 'combined';

    public const DELIVERY_STATUS_PENDING = 'pending';
    public const DELIVERY_STATUS_PROCESSING = 'processing';
    public const DELIVERY_STATUS_DELIVERED = 'delivered';
    public const DELIVERY_STATUS_CANCELED = 'canceled';


    // RELATIONS
    /**
     * items
     *
     * @return void
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class, 'invoice_id');
    }

    /**
     * payments
     *
     * @return void
     */
    public function payments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
    }

    /**
     * What is still owed on this invoice: its total minus every payment actually
     * recorded against it — cash, bank and customer-balance alike. The customer's
     * opening balance never offsets an invoice on its own; it only counts here
     * once it has been applied as a balance payment on this invoice.
     */
    public function dueAmount(): float
    {
        return round((float) $this->total - (float) $this->payments()->sum('amount'), 2);
    }

    // MUTATORS & ACCESSORS
    /**
     * getTotalPaidAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getTotalPaidAttribute($value)
    {
        return make2dec($value);
    }
    /**
     * getTotalAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getTotalAttribute($value)
    {
        return make2dec($value);
    }

    /**
     * getTokenAttribute
     *
     * @param  mixed $value
     * @return void
     */
    public function getTokenAttribute($value)
    {
        return route('invoice.live_url', $value);
    }

    /**
     * customerInfo
     *
     * @return void
     */
    public function customerInfo()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    /**
     * warehouse
     *
     * @return void
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
    public function platforms()
    {
        return $this->hasMany(InvoicePlatform::class)->where('is_active', true);
    }
}
