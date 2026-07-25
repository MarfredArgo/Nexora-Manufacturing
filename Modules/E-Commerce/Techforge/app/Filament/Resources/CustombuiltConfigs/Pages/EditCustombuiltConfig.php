<?php

namespace Modules\Ecommerce\Filament\Resources\CustombuiltConfigs\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\CustombuiltConfigs\CustombuiltConfigResource;

class EditCustombuiltConfig extends EditRecord
{
    protected static string $resource = CustombuiltConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
