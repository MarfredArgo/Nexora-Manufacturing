<?php

namespace Modules\Ecommerce\Filament\Resources\PcCases\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\PcCases\PcCaseResource;

class ListPcCases extends ListRecords
{
    protected static string $resource = PcCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
