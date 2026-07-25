<?php

namespace Modules\Ecommerce\Filament\Resources\AccessorySpeakerSystems\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Ecommerce\Filament\Resources\AccessorySpeakerSystems\AccessorySpeakerSystemResource;

class EditAccessorySpeakerSystem extends EditRecord
{
    protected static string $resource = AccessorySpeakerSystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
