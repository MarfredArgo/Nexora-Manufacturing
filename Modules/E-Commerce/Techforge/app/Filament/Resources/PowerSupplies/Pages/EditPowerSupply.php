<?php

namespace Modules\Ecommerce\Filament\Resources\PowerSupplies\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\PowerSupplies\PowerSupplyResource;

class EditPowerSupply extends EditRecord
{
    protected static string $resource = PowerSupplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
