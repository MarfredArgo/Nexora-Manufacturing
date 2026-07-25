<?php

namespace Modules\Ecommerce\Filament\Resources\PrebuiltConfigs\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\PrebuiltConfigs\PrebuiltConfigResource;

class EditPrebuiltConfig extends EditRecord
{
    protected static string $resource = PrebuiltConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
