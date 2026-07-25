<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::connection('procurement')->statement(<<<SQL
            CREATE TABLE IF NOT EXISTS suppliers (
                id BIGSERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                contact_person VARCHAR(255),
                email VARCHAR(255),
                phone VARCHAR(255),
                address VARCHAR(255),
                badge_color VARCHAR(10) DEFAULT '#2f6fed' NOT NULL,
                status VARCHAR(255) DEFAULT 'active' NOT NULL,
                brand VARCHAR(255),
                product_items TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT suppliers_status_check CHECK (status IN ('active', 'inactive', 'blacklisted'))
            )
        SQL);

        DB::connection('procurement')->statement(<<<SQL
            CREATE TABLE IF NOT EXISTS supplier_products (
                id BIGSERIAL PRIMARY KEY,
                supplier_id BIGINT NOT NULL,
                name VARCHAR(255) NOT NULL,
                sku VARCHAR(255),
                unit_price NUMERIC(14, 2) DEFAULT 0 NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT supplier_products_supplier_id_foreign
                    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
            )
        SQL);

        DB::connection('procurement')->statement(<<<SQL
            CREATE TABLE IF NOT EXISTS purchase_orders (
                id BIGSERIAL PRIMARY KEY,
                po_number VARCHAR(255) NOT NULL UNIQUE,
                supplier_id BIGINT NOT NULL,
                qty INTEGER DEFAULT 1 NOT NULL,
                amount NUMERIC(14, 2) DEFAULT 0 NOT NULL,
                status VARCHAR(255) DEFAULT 'pending' NOT NULL,
                priority VARCHAR(255) DEFAULT 'normal' NOT NULL,
                order_date DATE NOT NULL,
                expected_delivery_date DATE,
                created_by VARCHAR(255),
                remarks TEXT,
                item VARCHAR(255),
                brand VARCHAR(255),
                unit_price NUMERIC(14, 2) DEFAULT 0 NOT NULL,
                requisition_id BIGINT,
                requisition_reference VARCHAR(255),
                department VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT purchase_orders_supplier_id_foreign
                    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
                CONSTRAINT purchase_orders_status_check CHECK (
                    status IN ('pending', 'processing', 'approved', 'rejected', 'cancelled', 'completed')
                ),
                CONSTRAINT purchase_orders_priority_check CHECK (
                    priority IN ('low', 'normal', 'high', 'urgent')
                )
            )
        SQL);

        DB::connection('procurement')->statement(<<<SQL
            CREATE TABLE IF NOT EXISTS purchase_order_items (
                id BIGSERIAL PRIMARY KEY,
                purchase_order_id BIGINT NOT NULL,
                supplier_product_id BIGINT,
                name VARCHAR(255) NOT NULL,
                qty INTEGER DEFAULT 1 NOT NULL,
                unit_price NUMERIC(14, 2) DEFAULT 0 NOT NULL,
                amount NUMERIC(14, 2) DEFAULT 0 NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT purchase_order_items_purchase_order_id_foreign
                    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
                CONSTRAINT purchase_order_items_supplier_product_id_foreign
                    FOREIGN KEY (supplier_product_id) REFERENCES supplier_products(id) ON DELETE SET NULL
            )
        SQL);

        DB::connection('procurement')->statement(<<<SQL
            CREATE TABLE IF NOT EXISTS deliveries (
                id BIGSERIAL PRIMARY KEY,
                shipment_number VARCHAR(255) NOT NULL UNIQUE,
                purchase_order_id BIGINT,
                supplier_id BIGINT,
                status VARCHAR(255) DEFAULT 'pending' NOT NULL,
                qty INTEGER,
                qty_expected INTEGER,
                items VARCHAR(255),
                remarks TEXT,
                delivery_date DATE NOT NULL,
                estimated_arrival DATE,
                actual_arrival DATE,
                tracking_number VARCHAR(255),
                carrier VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT deliveries_purchase_order_id_foreign
                    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE SET NULL,
                CONSTRAINT deliveries_supplier_id_foreign
                    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
                CONSTRAINT deliveries_status_check CHECK (
                    status IN ('pending', 'scheduled', 'intransit', 'delivered', 'delayed', 'cancelled', 'completed')
                )
            )
        SQL);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('procurement')->statement('DROP TABLE IF EXISTS deliveries CASCADE');
        DB::connection('procurement')->statement('DROP TABLE IF EXISTS purchase_order_items CASCADE');
        DB::connection('procurement')->statement('DROP TABLE IF EXISTS purchase_orders CASCADE');
        DB::connection('procurement')->statement('DROP TABLE IF EXISTS supplier_products CASCADE');
        DB::connection('procurement')->statement('DROP TABLE IF EXISTS suppliers CASCADE');
    }
};
