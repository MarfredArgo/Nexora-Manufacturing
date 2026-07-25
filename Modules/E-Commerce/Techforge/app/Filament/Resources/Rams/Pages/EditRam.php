<?php

namespace Modules\Ecommerce\Filament\Resources\Rams\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\Rams\RamResource;

class EditRam extends EditRecord
{
    protected static string $resource = RamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
