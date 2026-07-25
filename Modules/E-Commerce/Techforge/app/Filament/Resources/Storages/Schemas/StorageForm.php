<?php

namespace Modules\Ecommerce\Filament\Resources\Storages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StorageForm
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
                TextInput::make('type')
                    ->required(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('cache'),
                TextInput::make('form_factor'),
                TextInput::make('interface'),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('brand'),
            ]);
    }
}
