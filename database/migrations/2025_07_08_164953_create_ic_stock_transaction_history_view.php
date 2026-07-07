<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW ic_stock_transaction_history_view AS

            -- Inbound (Purchase Item Receives)
            SELECT
                pir.created_at AS date_time,
                p.id AS product_id,
                w.id AS warehouse_id,
                p.name AS product,
                p.sku AS sku,
                w.name AS warehouse,
                CONCAT(s.first_name, ' ', s.last_name) AS employee_source,
                'Inbound' AS type,
                pir.quantity AS quantity,
                pr.purchase_number AS reference,
                prcv.id AS document_id,
                NULL AS stock_after,
                'Invoice import' AS comments

            FROM ic_purchase_item_receives pir
            JOIN ic_products p ON pir.product_id = p.id
            JOIN ic_purchase_receives prcv ON pir.purchase_receive_id = prcv.id
            JOIN ic_purchases pr ON pr.id = prcv.purchase_id
            JOIN ic_warehouses w ON pr.warehouse_id = w.id
            LEFT JOIN ic_suppliers s ON pr.supplier_id = s.id

            UNION ALL

            -- Outbound (Withdrawals from Invoices)
            SELECT
                ii.created_at AS date_time,
                p.id AS product_id,
                w.id AS warehouse_id,
                p.name AS product,
                p.sku AS sku,
                w.name AS warehouse,
                CONCAT(c.first_name, ' ', c.last_name) AS employee_source,
                'Withdrawal' AS type,
                -ii.quantity AS quantity,
                i.id AS reference,
                i.id AS document_id,
                NULL AS stock_after,
                'Withdrawn' AS comments

            FROM ic_invoice_items ii
            JOIN ic_invoices i ON i.id = ii.invoice_id
            JOIN ic_products p ON ii.product_id = p.id
            LEFT JOIN ic_customers c ON i.customer_id = c.id
            JOIN ic_warehouses w ON w.id = (
                SELECT warehouse_id FROM ic_purchases
                WHERE created_by = i.created_by
                LIMIT 1
            )
            WHERE i.is_withdrawal = 1;
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS ic_stock_transaction_history_view");
    }
};
