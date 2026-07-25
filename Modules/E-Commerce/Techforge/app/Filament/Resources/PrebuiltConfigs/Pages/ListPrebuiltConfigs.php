<?php

namespace Modules\Ecommerce\Filament\Resources\PrebuiltConfigs\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\PrebuiltConfigs\PrebuiltConfigResource;

class ListPrebuiltConfigs extends ListRecords
{
    protected static string $resource = PrebuiltConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
