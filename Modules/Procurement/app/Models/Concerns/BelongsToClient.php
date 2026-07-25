<?php

namespace Modules\Procurement\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait BelongsToClient
{
    public function getConnectionName(): ?string
    {
        return 'procurement';
    }

    protected static function bootBelongsToClient(): void
    {
        static::addGlobalScope('client', function (Builder $query): void {
            if (! Schema::connection('procurement')->hasColumn($query->getModel()->getTable(), 'client_id')) {
                // Never leak legacy standalone Procurement records while the
                // database is waiting for its explicit client-key upgrade.
                $query->whereRaw('1 = 0');

                return;
            }

            $query->where($query->getModel()->getTable().'.client_id', session('employee_client_id'));
        });

        static::creating(function ($model): void {
            $model->client_id = session('employee_client_id');
        });
    }
}
