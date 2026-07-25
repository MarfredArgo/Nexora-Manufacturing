<?php

namespace Modules\Ecommerce\Filament\Resources\PcCases\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\PcCases\PcCaseResource;

class EditPcCase extends EditRecord
{
    protected static string $resource = PcCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
