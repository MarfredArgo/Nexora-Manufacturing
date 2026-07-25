<?php

namespace Modules\Finance\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait BelongsToClient
{
    public function getConnectionName(): ?string
    {
        return 'finance';
    }

    protected static function bootBelongsToClient(): void
    {
        static::addGlobalScope('finance-client', function (Builder $query): void {
            if (! Schema::connection('finance')->hasColumn($query->getModel()->getTable(), 'nexora_client_id')) {
                $query->whereRaw('1 = 0');
                return;
            }

            if (config('nexora.root_admin_module_testing') && auth()->user()?->role === 'root_admin') {
                return;
            }

            $clientId = session('employee_client_id');
            if (! $clientId) {
                $query->whereRaw('1 = 0');
                return;
            }

            $query->where($query->getModel()->qualifyColumn('nexora_client_id'), $clientId);
        });

        static::creating(function ($model): void {
            if (! $model->nexora_client_id && ($clientId = session('employee_client_id'))) {
                $model->nexora_client_id = $clientId;
            }
        });
    }
}
