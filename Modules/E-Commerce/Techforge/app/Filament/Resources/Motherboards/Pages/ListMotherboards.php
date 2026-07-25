<?php

namespace Modules\Ecommerce\Filament\Resources\Motherboards\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\Motherboards\MotherboardResource;

class ListMotherboards extends ListRecords
{
    protected static string $resource = MotherboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
