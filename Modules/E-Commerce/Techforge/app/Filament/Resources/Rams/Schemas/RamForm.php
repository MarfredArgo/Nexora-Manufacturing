<?php

namespace Modules\Ecommerce\Filament\Resources\Rams\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RamForm
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
                TextInput::make('generation')
                    ->required(),
                TextInput::make('capacity')
                    ->required()
                    ->numeric(),
                TextInput::make('speed')
                    ->required()
                    ->numeric(),
                TextInput::make('modules'),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('brand'),
            ]);
    }
}
