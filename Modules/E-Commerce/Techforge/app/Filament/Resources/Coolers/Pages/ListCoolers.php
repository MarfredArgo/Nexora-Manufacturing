<?php

namespace Modules\Ecommerce\Filament\Resources\Coolers\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\Coolers\CoolerResource;

class ListCoolers extends ListRecords
{
    protected static string $resource = CoolerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
