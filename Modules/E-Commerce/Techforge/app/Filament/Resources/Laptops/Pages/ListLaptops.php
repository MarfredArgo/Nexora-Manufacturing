<?php

namespace Modules\Ecommerce\Filament\Resources\Laptops\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\Laptops\LaptopResource;

class ListLaptops extends ListRecords
{
    protected static string $resource = LaptopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
