<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Brand;
use App\Models\Account;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Expenses;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Attribute;
use App\Models\Warehouse;
use App\Models\SaleReturn;
use App\Models\Transaction;
use App\Models\WeightUnit;
use App\Models\InvoiceItem;
use App\Models\Manufacturer;
use App\Models\ProductStock;
use App\Models\AttributeItem;
use App\Models\InvoicePayment;
use App\Models\PurchaseItem;
use App\Models\PurchaseReceive;
use App\Models\SaleReturnItems;
use App\Models\ProductCategory;
use App\Models\ExpensesCategory;
use App\Models\ExpensesItem;
use App\Models\MeasurementUnit;
use App\Models\PurchaseItemReceive;
use App\Models\SaleReturnRequest;
use App\Models\SaleReturnItemRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $admin = User::first();
            $adminId = $admin->id;

            // ── 1. Warehouse ──────────────────────────────────────────────
            $warehouse = Warehouse::firstOrCreate(
                ['name' => 'Main Warehouse'],
                [
                    'email'        => 'main@bulbinventory.test',
                    'phone'        => '01700000001',
                    'company_name' => 'Bulb Inventory Co.',
                    'address_1'    => '123 Warehouse Street',
                    'address_2'    => 'Industrial Area',
                    'is_default'   => false,
                    'status'       => 'active',
                    'created_by'   => $adminId,
                    'updated_by'   => $adminId,
                ]
            );

            // Use default warehouse if it exists, otherwise use ours
            $defaultWarehouse = Warehouse::where('is_default', true)->first() ?? $warehouse;

            // ── 2. Product Category ───────────────────────────────────────
            $category = ProductCategory::firstOrCreate(
                ['name' => 'Lighting'],
                [
                    'desc'       => 'Lighting products and accessories',
                    'status'     => 'active',
                    'parent_id'  => null,
                    'position'   => 1,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            // ── 3. Brand ──────────────────────────────────────────────────
            $brand = Brand::firstOrCreate(
                ['name' => 'LumiBright'],
                [
                    'desc'       => 'Premium lighting solutions',
                    'status'     => 'active',
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            // ── 4. Manufacturer ───────────────────────────────────────────
            $manufacturer = Manufacturer::firstOrCreate(
                ['name' => 'LumiBright Manufacturing Ltd.'],
                [
                    'desc'       => 'LED bulb manufacturer since 2010',
                    'status'     => 'active',
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            // ── 5. Weight Unit ────────────────────────────────────────────
            $weightUnit = WeightUnit::firstOrCreate(
                ['name' => 'Kilogram (kg)'],
                ['created_by' => $adminId, 'updated_by' => $adminId]
            );

            // ── 6. Measurement Unit ───────────────────────────────────────
            $measurementUnit = MeasurementUnit::firstOrCreate(
                ['name' => 'Piece (pcs)'],
                ['created_by' => $adminId, 'updated_by' => $adminId]
            );

            // ── 7. Attributes & Items ─────────────────────────────────────
            $colorAttr = Attribute::firstOrCreate(
                ['name' => 'Color'],
                ['status' => 'active', 'created_by' => $adminId, 'updated_by' => $adminId]
            );
            AttributeItem::firstOrCreate(
                ['attribute_id' => $colorAttr->id, 'name' => 'Warm White'],
                ['created_by' => $adminId, 'updated_by' => $adminId]
            );
            AttributeItem::firstOrCreate(
                ['attribute_id' => $colorAttr->id, 'name' => 'Cool White'],
                ['created_by' => $adminId, 'updated_by' => $adminId]
            );

            $wattageAttr = Attribute::firstOrCreate(
                ['name' => 'Wattage'],
                ['status' => 'active', 'created_by' => $adminId, 'updated_by' => $adminId]
            );
            AttributeItem::firstOrCreate(
                ['attribute_id' => $wattageAttr->id, 'name' => '9W'],
                ['created_by' => $adminId, 'updated_by' => $adminId]
            );
            AttributeItem::firstOrCreate(
                ['attribute_id' => $wattageAttr->id, 'name' => '12W'],
                ['created_by' => $adminId, 'updated_by' => $adminId]
            );

            // ── 8. Product ────────────────────────────────────────────────
            $product = Product::firstOrCreate(
                ['sku' => 'BULB-LED-001'],
                [
                    'category_id'        => $category->id,
                    'brand_id'           => $brand->id,
                    'manufacturer_id'    => $manufacturer->id,
                    'weight_unit_id'     => $weightUnit->id,
                    'measurement_unit_id' => $measurementUnit->id,
                    'name'               => 'LED Bulb 9W',
                    'barcode'            => '8901234567890',
                    'price'              => 8.50,
                    'customer_buying_price' => 6.00,
                    'weight'             => 0.05,
                    'tax_status'         => 'excluded',
                    'status'             => 'active',
                    'available_for'      => 'all',
                    'is_variant'         => false,
                    'is_batch_product'   => false,
                    'stock'              => 100,
                    'notes'              => 'Energy-saving LED bulb, E27 base',
                    'created_by'         => $adminId,
                    'updated_by'         => $adminId,
                ]
            );

            // ── 9. Product Stock ──────────────────────────────────────────
            $productStock = ProductStock::firstOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => $defaultWarehouse->id],
                [
                    'quantity'          => 100,
                    'price'             => 8.50,
                    'customer_buying_price' => 6.00,
                    'manage_stock'      => true,
                    'backorders_allowed' => false,
                ]
            );

            // ── 9b. Second Product (sub-product of LED Bulb) ─────────────
            $driverProduct = Product::firstOrCreate(
                ['sku' => 'DRV-LED-001'],
                [
                    'category_id'         => $category->id,
                    'brand_id'            => $brand->id,
                    'manufacturer_id'     => $manufacturer->id,
                    'weight_unit_id'      => $weightUnit->id,
                    'measurement_unit_id' => $measurementUnit->id,
                    'name'                => 'LED Driver 9W',
                    'barcode'             => '8901234567891',
                    'price'               => 2.50,
                    'customer_buying_price' => 1.80,
                    'tax_status'          => 'excluded',
                    'status'              => 'active',
                    'available_for'       => 'all',
                    'is_variant'          => false,
                    'is_batch_product'    => false,
                    'stock'               => 150,
                    'notes'               => 'Internal LED driver component for 9W bulb',
                    'created_by'          => $adminId,
                    'updated_by'          => $adminId,
                ]
            );

            ProductStock::firstOrCreate(
                ['product_id' => $driverProduct->id, 'warehouse_id' => $defaultWarehouse->id],
                [
                    'quantity'           => 150,
                    'price'              => 2.50,
                    'customer_buying_price' => 1.80,
                    'manage_stock'       => true,
                    'backorders_allowed' => false,
                ]
            );

            // Link LED Driver as a sub-product of LED Bulb (1 driver deducted per bulb sold)
            $product->subProducts()->syncWithoutDetaching([
                $driverProduct->id => ['quantity' => 1],
            ]);

            // ── 10. Customer ──────────────────────────────────────────────
            $customer = Customer::firstOrCreate(
                ['email' => 'john.doe@example.com'],
                [
                    'first_name'     => 'John',
                    'last_name'      => 'Doe',
                    'phone'          => '01711111111',
                    'company'        => 'Doe Electricals',
                    'address_line_1' => '45 Main Road',
                    'city'           => null,
                    'country'        => null,
                    'status'          => 'active',
                    'is_verified'     => true,
                    'type'            => 'customer',
                    'opening_balance' => 0,
                    'created_by'     => $adminId,
                    'updated_by'     => $adminId,
                ]
            );

            // Customer with negative opening balance (has outstanding dues)
            Customer::firstOrCreate(
                ['email' => 'sara.negative@example.com'],
                [
                    'first_name'      => 'Sara',
                    'last_name'       => 'Ahmed',
                    'phone'           => '01733333333',
                    'company'         => 'Ahmed Traders',
                    'address_line_1'  => '77 Trade Center',
                    'city'            => null,
                    'country'         => null,
                    'status'          => 'active',
                    'is_verified'     => true,
                    'type'            => 'customer',
                    'opening_balance' => -2500.00,
                    'created_by'      => $adminId,
                    'updated_by'      => $adminId,
                ]
            );

            // ── 11. Supplier ──────────────────────────────────────────────
            $supplier = Supplier::firstOrCreate(
                ['email' => 'supply@techparts.com'],
                [
                    'first_name'     => 'Tech',
                    'last_name'      => 'Parts Ltd.',
                    'phone'          => '01722222222',
                    'company'        => 'Tech Parts Ltd.',
                    'address_line_1' => '12 Industrial Zone',
                    'city'           => null,
                    'country'        => null,
                    'status'         => 'active',
                    'opening_balance' => 0,
                    'created_by'     => $adminId,
                    'updated_by'     => $adminId,
                ]
            );

            // ── 12. Account (Cash) ────────────────────────────────────────
            $cashAccount = Account::firstOrCreate(
                ['name' => 'Main Cash Account'],
                [
                    'type'            => Account::TYPE_CASH,
                    'account_number'  => 'CASH-001',
                    'current_balance' => 50000.00,
                    'is_active'       => true,
                    'created_by'      => $adminId,
                    'updated_by'      => $adminId,
                ]
            );

            $bankAccount = Account::firstOrCreate(
                ['name' => 'Business Bank Account'],
                [
                    'type'            => Account::TYPE_BANK,
                    'account_number'  => '2100-0012345678',
                    'bank_name'       => 'Dutch-Bangla Bank',
                    'branch_name'     => 'Motijheel Branch',
                    'current_balance' => 200000.00,
                    'is_active'       => true,
                    'created_by'      => $adminId,
                    'updated_by'      => $adminId,
                ]
            );

            // ── 13. Purchase ──────────────────────────────────────────────
            $purchase = Purchase::firstOrCreate(
                ['purchase_number' => 'PO-2026-0001'],
                [
                    'supplier_id'  => $supplier->id,
                    'warehouse_id' => $defaultWarehouse->id,
                    'date'         => Carbon::now()->subDays(10)->toDateString(),
                    'notes'        => 'Initial stock purchase for LED Bulb 9W',
                    'total'        => 600.00,
                    'status'       => Purchase::STATUS_CONFIRMED,
                    'received'     => 1,
                    'created_by'   => $adminId,
                    'updated_by'   => $adminId,
                ]
            );

            $purchaseItem = PurchaseItem::firstOrCreate(
                ['purchase_id' => $purchase->id, 'product_id' => $product->id],
                [
                    'quantity'   => 100,
                    'price'      => 6.00,
                    'sub_total'  => 600.00,
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            $purchaseReceive = PurchaseReceive::firstOrCreate(
                ['purchase_id' => $purchase->id],
                [
                    'receive_date' => Carbon::now()->subDays(9)->toDateString(),
                    'total'        => 600.00,
                    'created_by'   => $adminId,
                    'updated_by'   => $adminId,
                ]
            );

            PurchaseItemReceive::firstOrCreate(
                ['purchase_item_id' => $purchaseItem->id],
                [
                    'purchase_receive_id' => $purchaseReceive->id,
                    'product_id'          => $product->id,
                    'quantity'            => 100,
                    'price'               => 6.00,
                    'sub_total'           => 600.00,
                ]
            );

            // ── 14. Invoice ───────────────────────────────────────────────
            $invoice = Invoice::firstOrCreate(
                ['token' => 'INV-2026-0001'],
                [
                    'date'          => Carbon::now()->subDays(5)->toDateString(),
                    'due_date'      => Carbon::now()->addDays(25)->toDateString(),
                    'customer_id'   => $customer->id,
                    'customer'      => [
                        'name'  => $customer->first_name . ' ' . $customer->last_name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                    ],
                    'billing_info'  => [
                        'address' => $customer->address_line_1,
                        'city'    => $customer->city,
                        'country' => $customer->country,
                    ],
                    'shipping_info' => [
                        'address' => $customer->address_line_1,
                        'city'    => $customer->city,
                        'country' => $customer->country,
                    ],
                    'items_data'    => '[]',
                    'tax_amount'    => 0,
                    'discount_amount' => 0,
                    'total'         => 85.00,
                    'total_paid'    => 85.00,
                    'last_paid'     => 85.00,
                    'payment_type'  => Invoice::PAYMENT_TYPE_CASH,
                    'status'        => Invoice::STATUS_PAID,
                    'warehouse_id'  => $defaultWarehouse->id,
                    'notes'         => '',
                    'created_by'    => $adminId,
                ]
            );

            $invoiceItem = InvoiceItem::firstOrCreate(
                ['invoice_id' => $invoice->id, 'product_id' => $product->id],
                [
                    'product_stock_id' => $productStock->id,
                    'product_name'     => $product->name,
                    'sku'              => $product->sku,
                    'quantity'         => 10,
                    'price'            => 8.50,
                    'sub_total'        => 85.00,
                    'discount'         => 0,
                    'tax'              => 0,
                ]
            );

            // Deduct stock for the invoice
            $productStock->decrement('quantity', 10);
            $product->decrement('stock', 10);

            // ── 15. Invoice Payment ───────────────────────────────────────
            $invoicePayment = InvoicePayment::firstOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'customer_id'  => $customer->id,
                    'date'         => $invoice->date,
                    'payment_type' => Invoice::PAYMENT_TYPE_CASH,
                    'amount'       => 85.00,
                    'notes'        => 'Full payment received in cash',
                    'created_by'   => $adminId,
                ]
            );

            // ── 16. Transaction (from invoice payment) ────────────────────
            Transaction::firstOrCreate(
                ['reference_id' => $invoice->id, 'reference_type' => Invoice::class],
                [
                    'account_id'    => $cashAccount->id,
                    'type'          => Transaction::TYPE_INVOICE_PAYMENT,
                    'amount'        => 85.00,
                    'balance_after' => $cashAccount->current_balance + 85.00,
                    'note'          => 'Payment for Invoice ' . $invoice->token,
                    'created_by'    => $adminId,
                ]
            );
            $cashAccount->increment('current_balance', 85.00);

            // ── 17. Sale Return ───────────────────────────────────────────
            $saleReturn = SaleReturn::firstOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'return_date'         => Carbon::now()->subDays(2)->toDateString(),
                    'return_note'         => 'Customer returned 1 defective bulb',
                    'return_total_amount' => 8.50,
                    'items_info'          => '[]',
                    'created_by'          => $adminId,
                    'updated_by'          => $adminId,
                ]
            );

            SaleReturnItems::firstOrCreate(
                ['sale_return_id' => $saleReturn->id, 'product_id' => $product->id],
                [
                    'invoice_item_id'  => $invoiceItem->id,
                    'product_name'     => $product->name,
                    'return_qty'       => 1,
                    'return_price'     => 8.50,
                    'return_sub_total' => 8.50,
                    'created_by'       => $adminId,
                    'updated_by'       => $adminId,
                ]
            );

            // Restore stock for the return
            $productStock->increment('quantity', 1);
            $product->increment('stock', 1);

            // ── 18. Sale Return Request ───────────────────────────────────
            $returnRequest = SaleReturnRequest::firstOrCreate(
                ['invoice_id' => $invoice->id, 'status' => SaleReturnRequest::STATUS_ACCEPTED],
                [
                    'warehouse_id'        => $defaultWarehouse->id,
                    'return_date'         => Carbon::now()->subDays(3)->toDateString(),
                    'return_note'         => 'Bulb stopped working after 2 days',
                    'return_total_amount' => 8.50,
                    'items_info'          => '[]',
                    'requested_by'        => $customer->id,
                    'status_updated_by'   => $adminId,
                    'status_updated_at'   => Carbon::now()->subDays(2),
                    'created_by'          => $adminId,
                    'updated_by'          => $adminId,
                ]
            );

            SaleReturnItemRequest::firstOrCreate(
                ['sale_return_request_id' => $returnRequest->id, 'product_id' => $product->id],
                [
                    'invoice_item_id'  => $invoiceItem->id,
                    'product_stock_id' => $productStock->id,
                    'product_name'     => $product->name,
                    'return_qty'       => 1,
                    'return_price'     => 8.50,
                    'return_sub_total' => 8.50,
                    'created_by'       => $adminId,
                    'updated_by'       => $adminId,
                ]
            );

            // ── 19. Expenses Category & Expense ───────────────────────────
            $expenseCategory = ExpensesCategory::firstOrCreate(
                ['name' => 'Office Supplies'],
                [
                    'desc'       => 'Stationery, printing, and general office supplies',
                    'status'     => 'active',
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );

            $expense = Expenses::firstOrCreate(
                ['title' => 'Monthly Office Supply Purchase'],
                [
                    'category_id' => $expenseCategory->id,
                    'date'        => Carbon::now()->subDays(7)->toDateString(),
                    'total'       => 1500.00,
                    'notes'       => 'Printer paper, pens, and folders for the month',
                    'expense_by'  => $adminId,
                    'created_by'  => $adminId,
                    'updated_by'  => $adminId,
                ]
            );

            ExpensesItem::firstOrCreate(
                ['expenses_id' => $expense->id, 'item_name' => 'Printer Paper (500 sheets)'],
                ['item_qty' => 5, 'amount' => 750.00, 'note' => 'A4 size']
            );
            ExpensesItem::firstOrCreate(
                ['expenses_id' => $expense->id, 'item_name' => 'Ballpoint Pens (box)'],
                ['item_qty' => 10, 'amount' => 500.00, 'note' => 'Blue ink']
            );
            ExpensesItem::firstOrCreate(
                ['expenses_id' => $expense->id, 'item_name' => 'Document Folders'],
                ['item_qty' => 25, 'amount' => 250.00, 'note' => 'A4 plastic folders']
            );

            // Expense transaction (debit from cash account)
            Transaction::firstOrCreate(
                ['reference_id' => $expense->id, 'reference_type' => Expenses::class],
                [
                    'account_id'    => $cashAccount->id,
                    'type'          => Transaction::TYPE_REDUCE,
                    'amount'        => 1500.00,
                    'balance_after' => $cashAccount->current_balance - 1500.00,
                    'note'          => 'Expense: ' . $expense->title,
                    'created_by'    => $adminId,
                ]
            );
            $cashAccount->decrement('current_balance', 1500.00);
        });
    }
}
