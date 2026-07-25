<?php

namespace Modules\Ecommerce\Filament\Resources\StorefrontListings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StorefrontListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image_url')->label('Image'),
            TextColumn::make('sku')->searchable(),
            TextColumn::make('name')->searchable(),
            TextColumn::make('price')->money('PHP')->sortable(),
            TextColumn::make('available_quantity')->label('Available')->badge()
                ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
            TextColumn::make('status')->badge(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
