<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryMice\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\AccessoryMice\AccessoryMouseResource;

class EditAccessoryMouse extends EditRecord
{
    protected static string $resource = AccessoryMouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
