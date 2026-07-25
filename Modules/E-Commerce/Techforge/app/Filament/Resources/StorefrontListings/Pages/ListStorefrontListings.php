<?php

namespace Modules\Ecommerce\Filament\Resources\StorefrontListings\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\StorefrontListings\StorefrontListingResource;

class ListStorefrontListings extends ListRecords
{
    protected static string $resource = StorefrontListingResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
