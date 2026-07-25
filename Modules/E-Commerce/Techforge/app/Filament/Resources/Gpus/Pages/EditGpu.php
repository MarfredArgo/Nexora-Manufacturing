<?php

namespace Modules\Ecommerce\Filament\Resources\Gpus\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\Gpus\GpuResource;

class EditGpu extends EditRecord
{
    protected static string $resource = GpuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
