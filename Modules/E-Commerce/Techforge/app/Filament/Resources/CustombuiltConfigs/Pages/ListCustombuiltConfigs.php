<?php

namespace Modules\Ecommerce\Filament\Resources\CustombuiltConfigs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\CustombuiltConfigs\CustombuiltConfigResource;

class ListCustombuiltConfigs extends ListRecords
{
    protected static string $resource = CustombuiltConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
