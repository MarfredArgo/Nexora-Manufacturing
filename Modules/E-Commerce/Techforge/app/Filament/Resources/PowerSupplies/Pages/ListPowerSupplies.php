<?php

namespace Modules\Ecommerce\Filament\Resources\PowerSupplies\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\PowerSupplies\PowerSupplyResource;

class ListPowerSupplies extends ListRecords
{
    protected static string $resource = PowerSupplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
