<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryMonitors\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\AccessoryMonitors\AccessoryMonitorResource;

class ListAccessoryMonitors extends ListRecords
{
    protected static string $resource = AccessoryMonitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
