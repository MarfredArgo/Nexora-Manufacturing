<?php

namespace Modules\Ecommerce\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Modules\Ecommerce\Support\EcommerceClientContext;

trait BelongsToClient
{
    public function getConnectionName(): ?string
    {
        return 'ecommerce';
    }

    public static function bootBelongsToClient(): void
    {
        static::addGlobalScope('ecommerce-client', function (Builder $builder): void {
            if (! Schema::connection('ecommerce')->hasColumn($builder->getModel()->getTable(), 'client_id')) {
                $builder->whereRaw('1 = 0');

                return;
            }

            $clientId = app(EcommerceClientContext::class)->clientId();

            if ($clientId === null) {
                $builder->whereNull($builder->getModel()->qualifyColumn('client_id'));

                return;
            }

            $builder->where($builder->getModel()->qualifyColumn('client_id'), $clientId);
        });

        static::creating(function ($model): void {
            $clientId = app(EcommerceClientContext::class)->clientId();

            if ($clientId === null) {
                throw new \LogicException('An ecommerce record cannot be created without a storefront client.');
            }

            $model->client_id = $clientId;
        });
    }
}
