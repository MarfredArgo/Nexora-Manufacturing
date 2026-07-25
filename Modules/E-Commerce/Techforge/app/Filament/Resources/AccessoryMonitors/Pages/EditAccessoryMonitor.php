<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryMonitors\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\AccessoryMonitors\AccessoryMonitorResource;

class EditAccessoryMonitor extends EditRecord
{
    protected static string $resource = AccessoryMonitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
