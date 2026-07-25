<?php

namespace Modules\Procurement\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Inventory\Models\Warehouse;

class ProcurementServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'procurement');
        $this->commands([
            \Modules\Procurement\Console\Commands\EnsureProcurementClientColumns::class,
            \Modules\Procurement\Console\Commands\InstallProcurementSchema::class,
            \Modules\Procurement\Console\Commands\BackfillProcurementClientId::class,
        ]);

        // Gate every Procurement route behind ProcurementAccess so only a
        // logged-in Procurement/Purchasing employee tied to a client can reach
        // it (client-based access, not just client-based data scoping).
        Route::middleware(['web', \Modules\Procurement\Http\Middleware\ProcurementAccess::class])
            ->prefix('procurement')
            ->name('procurement.')
            ->group(__DIR__.'/../../routes/web.php');

        // The PO modal is included by every Procurement page, not just the
        // purchase-order page. Supply its client-scoped warehouse list at the
        // shared partial level so a user cannot submit a PO with a typed or
        // foreign-client delivery address.
        View::composer('procurement::partials.modals', function ($view): void {
            $view->with('warehouses', Warehouse::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name', 'address']));

            // Next PO / shipment sequence, taken from the trailing number of the
            // highest existing PO/shipment for this client, so the "+ New PO" and
            // "+ Log Delivery" modals auto-fill the real next number instead of a
            // stale hardcoded counter (which collided and produced ugly suffixed
            // numbers). Only the last digit group is read — never the year.
            $clientId = (int) session('employee_client_id');
            $lastSeq = function (string $table, string $column) use ($clientId): int {
                try {
                    $procurement = DB::connection('procurement');
                    $schema = $procurement->getSchemaBuilder();
                    if (! $schema->hasTable($table)) {
                        return 0;
                    }
                    $query = $procurement->table($table);
                    if ($schema->hasColumn($table, 'client_id')) {
                        $query->where('client_id', $clientId);
                    }

                    return (int) ($query->pluck($column)
                        ->map(fn ($n) => preg_match('/(\d+)$/', (string) $n, $m) ? (int) $m[1] : 0)
                        ->max() ?? 0);
                } catch (\Exception $e) {
                    return 0;
                }
            };

            $view->with([
                'nextPoSeq' => $lastSeq('purchase_orders', 'po_number') + 1,
                'nextShipmentSeq' => $lastSeq('deliveries', 'shipment_number') + 1,
            ]);
        });

          // The sidebar is included as `procurement::partials.sidebar`, so the
          // composer must target that namespaced name — otherwise it never
          // fires and the notification/nav badge counts stay empty.
          View::composer('procurement::partials.sidebar', function ($view): void {
            $alerts = collect();
            try {
                $alerts = DB::connection('inventory')
                    ->table('stock_levels as sl')
                    ->join('items as i', 'sl.item_id', '=', 'i.id')
                    ->where('sl.stock', '<', 5)
                    ->orderBy('sl.stock', 'asc')
                    ->select('sl.stock', 'sl.reorder_threshold', 'i.name as item_name', 'i.sku')
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                $alerts = collect();
            }

            // Delivery + pending-PO counts for the nav badges (tenant-scoped,
            // defensive so a missing table/column never breaks the layout).
            $clientId = (int) session('employee_client_id');
            $deliveryCount = 0;
            $pendingPoCount = 0;
            try {
                $procurement = DB::connection('procurement');
                $schema = $procurement->getSchemaBuilder();
                if ($schema->hasTable('deliveries')) {
                    $query = $procurement->table('deliveries');
                    if ($schema->hasColumn('deliveries', 'client_id')) {
                        $query->where('client_id', $clientId);
                    }
                    $deliveryCount = $query->count();
                }
                if ($schema->hasTable('purchase_orders')) {
                    $query = $procurement->table('purchase_orders')->where('status', 'pending');
                    if ($schema->hasColumn('purchase_orders', 'client_id')) {
                        $query->where('client_id', $clientId);
                    }
                    $pendingPoCount = $query->count();
                }
            } catch (\Exception $e) {
                // leave the counts at 0
            }

            $requisitionCount = 0;
            try {
                $requisitionConnection = $this->resolveRequisitionConnection();
                if ($requisitionConnection && $requisitionConnection->getSchemaBuilder()->hasTable('requisitions')) {
                    if ($requisitionConnection->getSchemaBuilder()->hasColumn('requisitions', 'status')) {
                        $requisitionCount = $requisitionConnection->table('requisitions')
                            ->where(function ($query) {
                                $query->where('status', 'Pending')
                                    ->orWhere('status', 'pending');
                            })
                            ->count();
                    } else {
                        $requisitionCount = $requisitionConnection->table('requisitions')->count();
                    }
                }
            } catch (\Exception $e) {
                $requisitionCount = 0;
            }

            $view->with([
                'lowStockAlerts' => $alerts,
                'lowStockAlertCount' => $alerts->count(),
                'requisitionCount' => $requisitionCount,
                'deliveryCount' => $deliveryCount,
                'pendingPoCount' => $pendingPoCount,
            ]);
        });
    }

    private function resolveRequisitionConnection()
    {
        foreach (['order_fulfillment', 'inventory'] as $connection) {
            try {
                if (DB::connection($connection)->getSchemaBuilder()->hasTable('requisitions')) {
                    return DB::connection($connection);
                }
            } catch (\Exception $e) {
                // ignore broken or unavailable external DB connections
            }
        }

        return DB::connection('inventory');
    }
}
