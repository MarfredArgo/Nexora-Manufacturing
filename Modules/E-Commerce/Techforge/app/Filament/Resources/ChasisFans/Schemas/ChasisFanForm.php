<?php

namespace Modules\Ecommerce\Filament\Resources\ChasisFans\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChasisFanForm
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
                TextInput::make('size'),
                TextInput::make('rpm'),
                TextInput::make('airflow'),
                TextInput::make('noise_level'),
                TextInput::make('color'),
                Toggle::make('rgb')
                    ->required(),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('brand'),
            ]);
    }
}
