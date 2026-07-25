<?php

namespace Modules\Ecommerce\Filament\Resources\ChasisFans\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\ChasisFans\ChasisFanResource;

class ListChasisFans extends ListRecords
{
    protected static string $resource = ChasisFanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
