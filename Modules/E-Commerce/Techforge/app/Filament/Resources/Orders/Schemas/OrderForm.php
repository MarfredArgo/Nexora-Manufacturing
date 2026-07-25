<?php

namespace Modules\Ecommerce\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('total')
                    ->required()
                    ->numeric(),
                TextInput::make('shipping_fee')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('payment_method')
                    ->required(),
                TextInput::make('payment_status')
                    ->required()
                    ->default('unpaid'),
                TextInput::make('shipping_address')
                    ->required(),
                TextInput::make('tracking_number'),
            ]);
    }
}
