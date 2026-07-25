<?php

namespace Modules\Ecommerce\Filament\Resources\AccessorySpeakerSystems\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\AccessorySpeakerSystems\AccessorySpeakerSystemResource;

class ListAccessorySpeakerSystems extends ListRecords
{
    protected static string $resource = AccessorySpeakerSystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
