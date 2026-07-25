<?php

namespace Modules\Ecommerce\Filament\Resources\Laptops\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LaptopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('client_id')
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('brand'),
                TextInput::make('processor')
                    ->required(),
                TextInput::make('gpu')
                    ->required(),
                TextInput::make('ram')
                    ->required(),
                TextInput::make('storage')
                    ->required(),
                TextInput::make('display'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                FileUpload::make('image_url')
                    ->image(),
                Toggle::make('is_sold_out')
                    ->required(),
            ]);
    }
}
