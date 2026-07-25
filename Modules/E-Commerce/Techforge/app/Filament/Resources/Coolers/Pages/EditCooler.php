<?php

namespace Modules\Ecommerce\Filament\Resources\Coolers\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\Coolers\CoolerResource;

class EditCooler extends EditRecord
{
    protected static string $resource = CoolerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
