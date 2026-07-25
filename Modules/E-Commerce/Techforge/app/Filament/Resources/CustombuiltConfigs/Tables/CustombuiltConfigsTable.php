<?php

namespace Modules\Ecommerce\Filament\Resources\CustombuiltConfigs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustombuiltConfigsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                ImageColumn::make('image_url'),
                TextColumn::make('tier')
                    ->searchable(),
                TextColumn::make('intel_cpu_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amd_cpu_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('intel_motherboard_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amd_motherboard_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('intel_ram_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amd_ram_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gpu_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('storage_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('power_supply_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pc_case_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cooler_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('review_count')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
