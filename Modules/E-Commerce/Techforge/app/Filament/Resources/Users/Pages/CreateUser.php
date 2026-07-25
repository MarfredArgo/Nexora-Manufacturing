<?php

namespace Modules\Ecommerce\Filament\Resources\Users\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Ecommerce\Filament\Resources\Users\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
