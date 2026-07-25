<?php

namespace Modules\Ecommerce\Filament\Resources\PcCases\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PcCaseForm
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
                TextInput::make('max_mobo_size')
                    ->required()
                    ->numeric(),
                TextInput::make('max_gpu_length')
                    ->required()
                    ->numeric(),
                TextInput::make('type'),
                TextInput::make('color'),
                TextInput::make('side_panel'),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('brand'),
                TextInput::make('fans_included'),
            ]);
    }
}
