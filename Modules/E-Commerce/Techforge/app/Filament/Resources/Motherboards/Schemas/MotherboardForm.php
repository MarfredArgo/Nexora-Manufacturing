<?php

namespace Modules\Ecommerce\Filament\Resources\Motherboards\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MotherboardForm
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
                TextInput::make('socket')
                    ->required(),
                TextInput::make('form_factor')
                    ->required()
                    ->numeric(),
                TextInput::make('supported_ram_gen')
                    ->required(),
                TextInput::make('memory_max'),
                TextInput::make('memory_slots')
                    ->numeric(),
                TextInput::make('color'),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('brand'),
                Toggle::make('wifi')
                    ->required(),
            ]);
    }
}
