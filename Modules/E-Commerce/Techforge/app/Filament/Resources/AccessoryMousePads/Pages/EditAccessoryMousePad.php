<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryMousePads\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\AccessoryMousePads\AccessoryMousePadResource;

class EditAccessoryMousePad extends EditRecord
{
    protected static string $resource = AccessoryMousePadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
