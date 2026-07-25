<?php

namespace Modules\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Ecommerce\Models\Concerns\BelongsToClient;
use Modules\Ecommerce\Services\ListingAvailabilityService;
use Modules\Ecommerce\Support\EcommerceClientContext;

class StorefrontListing extends Model
{
    use BelongsToClient;

    protected $fillable = ['bom_id', 'sku', 'name', 'description', 'price', 'image_url', 'status'];

    protected $casts = ['price' => 'decimal:2'];

    protected $appends = ['available_quantity'];

    protected static function booted(): void
    {
        static::saving(function (self $listing): void {
            $clientId = (int) ($listing->client_id ?: app(EcommerceClientContext::class)->clientId());

            if (! $clientId || ! DB::connection('manufacturing')->table('product_boms')
                ->where('id', $listing->bom_id)
                ->where('client_id', $clientId)
                ->where('status', 'active')
                ->exists()) {
                throw new \LogicException('Select an active Bill of Materials owned by this client.');
            }
        });
    }

    public function getAvailableQuantityAttribute(): int
    {
        $clientId = (int) ($this->client_id ?: app(EcommerceClientContext::class)->clientId());

        return $clientId
            ? app(ListingAvailabilityService::class)->availableUnits($clientId, (int) $this->bom_id)
            : 0;
    }
}
