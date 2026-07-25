<?php

namespace Modules\Ecommerce\Filament\Resources\Cpus\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\Cpus\CpuResource;

class ListCpus extends ListRecords
{
    protected static string $resource = CpuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
