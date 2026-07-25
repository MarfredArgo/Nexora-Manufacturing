<?php

namespace Modules\Ecommerce\Filament\Resources\StorefrontListings\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Ecommerce\Filament\Resources\StorefrontListings\StorefrontListingResource;

class CreateStorefrontListing extends CreateRecord
{
    protected static string $resource = StorefrontListingResource::class;
}
