<?php

namespace Modules\Ecommerce\Filament\Resources\Laptops\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\Laptops\LaptopResource;

class EditLaptop extends EditRecord
{
    protected static string $resource = LaptopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
