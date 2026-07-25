<?php

namespace Modules\Ecommerce\Filament\Resources\Storages\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\Storages\StorageResource;

class EditStorage extends EditRecord
{
    protected static string $resource = StorageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
