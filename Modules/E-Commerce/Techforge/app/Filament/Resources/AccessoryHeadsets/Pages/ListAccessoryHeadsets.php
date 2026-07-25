<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryHeadsets\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\AccessoryHeadsets\AccessoryHeadsetResource;

class ListAccessoryHeadsets extends ListRecords
{
    protected static string $resource = AccessoryHeadsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
