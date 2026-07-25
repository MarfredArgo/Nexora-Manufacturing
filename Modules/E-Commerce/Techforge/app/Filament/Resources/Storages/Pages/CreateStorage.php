<?php

namespace Modules\Ecommerce\Filament\Resources\Storages\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Ecommerce\Filament\Resources\Storages\StorageResource;

class CreateStorage extends CreateRecord
{
    protected static string $resource = StorageResource::class;
}
