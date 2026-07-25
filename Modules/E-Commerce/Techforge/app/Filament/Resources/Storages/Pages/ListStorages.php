<?php

namespace Modules\Ecommerce\Filament\Resources\Storages\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\Storages\StorageResource;

class ListStorages extends ListRecords
{
    protected static string $resource = StorageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
