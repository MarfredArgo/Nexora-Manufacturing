<?php

namespace App\Services\Departments;

use App\Models\AIGeneration;
use App\Models\AIReport;
use Illuminate\Support\Facades\DB;

class BiService
{
    public function getSnapshot(): array
    {
        return [
            'connected_sources' => $this->connectedDepartmentsCount(),
            'total_sources' => $this->totalDepartmentsCount(),
            'total_records' => $this->totalRecordsSynced(),
            'reports_generated' => $this->reportsGeneratedCount(),
            'ai_generations' => $this->aiGenerationsCount(),
        ];
    }

    public function connectedDepartmentsCount(): int
    {
        $connected = 0;
        foreach ($this->departmentTables() as $dept => $tables) {
            foreach ($tables as $table) {
                try {
                    if (DB::table($table)->count() > 0) {
                        $connected++;
                        break;
                    }
                } catch (\Throwable) {
                }
            }
        }
        return $connected;
    }

    public function totalDepartmentsCount(): int
    {
        return 8;
    }

    public function totalRecordsSynced(): int
    {
        $total = 0;
        foreach ($this->departmentTables() as $tables) {
            foreach ($tables as $table) {
                try {
                    $total += DB::table($table)->count();
                } catch (\Throwable) {
                }
            }
        }
        return $total;
    }

    public function recordsByDepartment(): array
    {
        $counts = [];
        foreach ($this->departmentTables() as $dept => $tables) {
            $total = 0;
            foreach ($tables as $table) {
                try {
                    $total += DB::table($table)->count();
                } catch (\Throwable) {
                }
            }
            $counts[$dept] = $total;
        }
        return $counts;
    }

    public function reportsGeneratedCount(): int
    {
        try {
            return AIReport::count();
        } catch (\Throwable) {
            return 0;
        }
    }

    public function aiGenerationsCount(): int
    {
        try {
            return AIGeneration::count();
        } catch (\Throwable) {
            return 0;
        }
    }

    private function departmentTables(): array
    {
        return [
            'E-Commerce' => ['ecommerce_dept_products', 'ecommerce_dept_prebuilt_configs', 'ecommerce_dept_configurator_configs'],
            'Inventory' => ['inventory_dept_items', 'inventory_dept_stock_levels'],
            'Finance' => ['finance_dept_invoices'],
            'Manufacturing' => ['manufacturing_work_orders', 'manufacturing_qc_results', 'manufacturing_machines'],
            'Procurement' => ['procurement_orders'],
            'Fulfillment' => ['fulfillment_orders', 'fulfillment_shipments'],
            'Compliance' => ['compliance_risks'],
            'ITSM' => ['itsm_tickets'],
        ];
    }
}