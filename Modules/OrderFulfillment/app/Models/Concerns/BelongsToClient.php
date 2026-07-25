<?php

namespace Modules\OrderFulfillment\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToClient
{
    public function getConnectionName(): ?string { return 'order_fulfillment'; }
    protected static function bootBelongsToClient(): void
    {
        static::addGlobalScope('client', fn (Builder $query) => $query->where($query->getModel()->getTable().'.client_id', session('employee_client_id')));
        static::creating(fn ($model) => $model->client_id = session('employee_client_id'));
    }
}
