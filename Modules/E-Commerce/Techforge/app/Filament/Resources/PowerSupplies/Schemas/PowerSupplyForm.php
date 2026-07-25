<?php

namespace Modules\Ecommerce\Filament\Resources\PowerSupplies\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PowerSupplyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('client_id')
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('wattage')
                    ->required()
                    ->numeric(),
                TextInput::make('form_factor')
                    ->required(),
                TextInput::make('type'),
                TextInput::make('modular'),
                TextInput::make('color'),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('efficiency')
                    ->required()
                    ->default('Gold'),
                TextInput::make('brand'),
            ]);
    }
}
