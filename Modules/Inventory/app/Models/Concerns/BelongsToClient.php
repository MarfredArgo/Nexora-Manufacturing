<?php

namespace Modules\Inventory\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait BelongsToClient
{
    private static array $clientColumnCache = [];

    public function getConnectionName(): ?string
    {
        return 'inventory';
    }

    protected static function bootBelongsToClient(): void
    {
        static::addGlobalScope('client', function (Builder $query): void {
            $table = $query->getModel()->getTable();

            if (! isset(self::$clientColumnCache[$table])) {
                self::$clientColumnCache[$table] = Schema::connection('inventory')->hasColumn($table, 'client_id');
            }

            if (! self::$clientColumnCache[$table]) {
                $query->whereRaw('1 = 0');

                return;
            }

            if ($clientId = session('employee_client_id')) {
                $query->where($table.'.client_id', $clientId);

                return;
            }

            // No tenant in scope. Previously this left the query completely
            // unfiltered, exposing and allowing writes across every client.
            // Fail closed, except for the explicit root-admin testing bypass.
            if (! self::rootAdminModuleTesting()) {
                $query->whereRaw('1 = 0');
            }
        });

        static::creating(function ($model): void {
            if ($model->client_id) {
                return;
            }

            if ($clientId = session('employee_client_id')) {
                $model->client_id = $clientId;

                return;
            }

            // A null client_id never satisfies the client-scoped unique indexes,
            // so these rows silently duplicate stock levels and leak between
            // tenants. Refuse to write one.
            throw new \RuntimeException(sprintf(
                'Cannot persist %s without a client context (employee_client_id is not set).',
                static::class
            ));
        });
    }

    private static function rootAdminModuleTesting(): bool
    {
        return (bool) config('nexora.root_admin_module_testing')
            && auth()->user()?->role === 'root_admin';
    }
}
