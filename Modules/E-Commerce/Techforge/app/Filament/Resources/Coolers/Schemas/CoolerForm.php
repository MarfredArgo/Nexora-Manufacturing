<?php

namespace Modules\Ecommerce\Filament\Resources\Coolers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CoolerForm
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
                TextInput::make('fan_rpm'),
                TextInput::make('noise_level'),
                TextInput::make('color'),
                TextInput::make('radiator_size'),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('brand'),
            ]);
    }
}
