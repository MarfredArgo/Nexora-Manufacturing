<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryKeyboards\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\AccessoryKeyboards\AccessoryKeyboardResource;

class ListAccessoryKeyboards extends ListRecords
{
    protected static string $resource = AccessoryKeyboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
