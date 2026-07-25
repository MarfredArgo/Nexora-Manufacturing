<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryKeyboards\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\AccessoryKeyboards\AccessoryKeyboardResource;

class EditAccessoryKeyboard extends EditRecord
{
    protected static string $resource = AccessoryKeyboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
