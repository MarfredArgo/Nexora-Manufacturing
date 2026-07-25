<?php

namespace Modules\Ecommerce\Filament\Resources\Orders\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Ecommerce\Filament\Resources\Orders\OrderResource;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
