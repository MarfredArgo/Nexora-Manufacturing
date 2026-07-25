<?php

namespace Modules\Ecommerce\Filament\Resources\CustombuiltConfigs\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustombuiltConfigForm
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
                TextInput::make('tier')
                    ->required(),
                TextInput::make('intel_cpu_id')
                    ->tel()
                    ->required()
                    ->numeric(),
                TextInput::make('amd_cpu_id')
                    ->required()
                    ->numeric(),
                TextInput::make('intel_motherboard_id')
                    ->tel()
                    ->required()
                    ->numeric(),
                TextInput::make('amd_motherboard_id')
                    ->required()
                    ->numeric(),
                TextInput::make('intel_ram_id')
                    ->tel()
                    ->numeric(),
                TextInput::make('amd_ram_id')
                    ->numeric(),
                TextInput::make('gpu_id')
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
                TextInput::make('rating')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('review_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
