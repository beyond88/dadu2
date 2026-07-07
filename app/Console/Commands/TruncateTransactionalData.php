<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateTransactionalData extends Command
{
    protected $signature = 'db:truncate';
    protected $description = 'Truncate all transactional data, keeping settings and admin@inventory.com';

    private array $tables = [
        // Stock & Product history
        'product_stock_histories',
        'product_stocks',
        'product_cartons',
        'product_platforms',
        'product_relations',
        'product_attributes',
        'attribute_item_variation',
        'variation_platforms',
        'variations',
        'attribute_items',
        'attribute_platforms',
        'attributes',
        'product_category_platforms',
        'products',

        // Invoices
        'draft_invoice_items',
        'draft_invoices',
        'invoice_items',
        'invoice_payments',
        'invoice_platforms',
        'invoices',

        // Purchases
        'purchase_item_receives',
        'purchase_receives',
        'purchase_return_items',
        'purchase_returns',
        'purchase_items',
        'purchase_payments',
        'purchases',

        // Sale Returns
        'sale_return_item_requests',
        'sale_return_requests',
        'sale_return_items',
        'sale_returns',

        // Customers & Suppliers
        'customer_platforms',
        'customers',
        'suppliers',

        // Warehouse Transfers
        'warehouse_transfer_items',
        'warehouse_transfers',

        // Finance
        'expenses_files',
        'expenses_items',
        'expenses',
        'lc_expenses',
        'lcs',
        'loan_payments',
        'loans',
        'capital_payments',
        'capitals',
        'transactions',
        'user_wallet_histories',

        // Coupons
        'coupon_products',
        'coupons',

        // Misc
        'notifications',
        'jobs',
        'failed_jobs',
        'progress_tracking',
        'password_resets',
    ];

    public function handle()
    {
        $keepEmail = 'admin@inventory.com';

        $admin = DB::table('users')->where('email', $keepEmail)->first();

        if (!$admin) {
            $this->error("User [{$keepEmail}] not found in database. Aborting.");
            return 1;
        }

        $this->warn('The following will be TRUNCATED:');
        foreach ($this->tables as $table) {
            $this->line("  - {$table}");
        }
        $this->warn("All users except [{$keepEmail}] will be deleted.");
        $this->newLine();

        if (!$this->confirm('Are you sure? This cannot be undone.')) {
            $this->info('Cancelled.');
            return 0;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($this->tables as $table) {
            DB::table($table)->truncate();
            $this->line("  ✓ {$table}");
        }

        // Users — delete everyone except admin
        DB::table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->where('model_id', '!=', $admin->id)
            ->delete();

        DB::table('model_has_permissions')
            ->where('model_type', 'App\\Models\\User')
            ->where('model_id', '!=', $admin->id)
            ->delete();

        DB::table('user_warehouses')
            ->where('user_id', '!=', $admin->id)
            ->delete();

        DB::table('users')
            ->where('email', '!=', $keepEmail)
            ->delete();

        $this->line("  ✓ users (kept: {$keepEmail})");

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->newLine();
        $this->info('Done. All transactional data truncated successfully.');

        return 0;
    }
}
