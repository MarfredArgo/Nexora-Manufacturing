<?php

namespace Modules\Ecommerce\Filament\Resources\Gpus\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Ecommerce\Filament\Resources\Gpus\GpuResource;

class ListGpus extends ListRecords
{
    protected static string $resource = GpuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
