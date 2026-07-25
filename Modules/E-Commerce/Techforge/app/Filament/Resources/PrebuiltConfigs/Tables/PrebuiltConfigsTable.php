<?php

namespace Modules\Ecommerce\Filament\Resources\PrebuiltConfigs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrebuiltConfigsTable
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
                TextColumn::make('cpu_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gpu_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('motherboard_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ram_id')
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
