<?php

namespace Modules\Ecommerce\Filament\Resources\Motherboards\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\Motherboards\MotherboardResource;

class EditMotherboard extends EditRecord
{
    protected static string $resource = MotherboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
