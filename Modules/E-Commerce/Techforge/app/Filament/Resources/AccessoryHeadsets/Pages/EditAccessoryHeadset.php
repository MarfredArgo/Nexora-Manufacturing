<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryHeadsets\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\AccessoryHeadsets\AccessoryHeadsetResource;

class EditAccessoryHeadset extends EditRecord
{
    protected static string $resource = AccessoryHeadsetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
