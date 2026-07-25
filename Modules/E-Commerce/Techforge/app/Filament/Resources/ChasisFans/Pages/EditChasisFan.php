<?php

namespace Modules\Ecommerce\Filament\Resources\ChasisFans\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\ChasisFans\ChasisFanResource;

class EditChasisFan extends EditRecord
{
    protected static string $resource = ChasisFanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
