<?php

namespace Modules\Ecommerce\Filament\Resources\Rams\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\Rams\RamResource;

class ListRams extends ListRecords
{
    protected static string $resource = RamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
