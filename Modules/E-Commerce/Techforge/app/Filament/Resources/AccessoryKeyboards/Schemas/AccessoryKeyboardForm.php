<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryKeyboards\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AccessoryKeyboardForm
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
                TextInput::make('type'),
                TextInput::make('switch_type'),
                TextInput::make('size_layout'),
                Toggle::make('is_wireless')
                    ->required(),
            ]);
    }
}
