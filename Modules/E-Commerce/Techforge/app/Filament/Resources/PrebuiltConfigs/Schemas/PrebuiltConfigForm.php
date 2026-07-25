<?php

namespace Modules\Ecommerce\Filament\Resources\PrebuiltConfigs\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PrebuiltConfigForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('client_id')
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                FileUpload::make('image_url')
                    ->image(),
                TextInput::make('cpu_id')
                    ->required()
                    ->numeric(),
                TextInput::make('gpu_id')
                    ->required()
                    ->numeric(),
                TextInput::make('motherboard_id')
                    ->required()
                    ->numeric(),
                TextInput::make('ram_id')
                    ->required()
                    ->numeric(),
                TextInput::make('storage_id')
                    ->required()
                    ->numeric(),
                TextInput::make('power_supply_id')
                    ->required()
                    ->numeric(),
                TextInput::make('pc_case_id')
                    ->numeric(),
                TextInput::make('cooler_id')
                    ->numeric(),
            ]);
    }
}
