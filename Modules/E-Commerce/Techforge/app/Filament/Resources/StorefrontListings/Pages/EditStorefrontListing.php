<?php

namespace Modules\Ecommerce\Filament\Resources\StorefrontListings\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\StorefrontListings\StorefrontListingResource;

class EditStorefrontListing extends EditRecord
{
    protected static string $resource = StorefrontListingResource::class;
}
