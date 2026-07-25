<?php

namespace Modules\Manufacturing\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait BelongsToClient
{
    public function getConnectionName(): ?string
    {
        return 'manufacturing';
    }

    protected static function bootBelongsToClient(): void
    {
        static::addGlobalScope('manufacturing-client', function (Builder $query): void {
            if (! Schema::connection('manufacturing')->hasColumn($query->getModel()->getTable(), 'client_id')) {
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

            $query->where($query->getModel()->qualifyColumn('client_id'), $clientId);
        });

        static::creating(function ($model): void {
            if (! $model->client_id && ($clientId = session('employee_client_id'))) {
                $model->client_id = $clientId;
            }
        });
    }
}
