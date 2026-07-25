<?php

namespace Modules\Ecommerce\Filament\Resources\Cpus\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\Cpus\CpuResource;

class EditCpu extends EditRecord
{
    protected static string $resource = CpuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
