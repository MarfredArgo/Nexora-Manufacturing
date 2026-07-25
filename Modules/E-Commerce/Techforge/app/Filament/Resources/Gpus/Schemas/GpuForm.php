<?php

namespace Modules\Ecommerce\Filament\Resources\Gpus\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GpuForm
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
                TextInput::make('tdp')
                    ->required()
                    ->numeric(),
                TextInput::make('length_mm')
                    ->required()
                    ->numeric(),
                TextInput::make('chipset'),
                TextInput::make('memory')
                    ->numeric(),
                TextInput::make('boost_clock'),
                TextInput::make('color'),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('brand'),
            ]);
    }
}
