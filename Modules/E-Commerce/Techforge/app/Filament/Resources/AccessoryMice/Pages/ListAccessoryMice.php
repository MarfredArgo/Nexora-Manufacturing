<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryMice\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\AccessoryMice\AccessoryMouseResource;

class ListAccessoryMice extends ListRecords
{
    protected static string $resource = AccessoryMouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
