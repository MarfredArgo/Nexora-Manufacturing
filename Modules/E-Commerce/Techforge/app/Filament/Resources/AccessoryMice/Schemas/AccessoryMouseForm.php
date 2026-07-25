<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryMice\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AccessoryMouseForm
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
                TextInput::make('brand'),
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('image_url')
                    ->image(),
                Toggle::make('is_sold_out')
                    ->required(),
                TextInput::make('dpi')
                    ->numeric(),
                Toggle::make('is_wireless')
                    ->required(),
                TextInput::make('sensor_type'),
                TextInput::make('buttons')
                    ->numeric(),
            ]);
    }
}
