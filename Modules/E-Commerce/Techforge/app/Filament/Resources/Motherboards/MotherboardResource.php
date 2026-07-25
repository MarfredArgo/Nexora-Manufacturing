<?php

namespace Modules\Ecommerce\Filament\Resources\Motherboards;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\Motherboards\Pages\CreateMotherboard;
use Modules\Ecommerce\Filament\Resources\Motherboards\Pages\EditMotherboard;
use Modules\Ecommerce\Filament\Resources\Motherboards\Pages\ListMotherboards;
use Modules\Ecommerce\Filament\Resources\Motherboards\Schemas\MotherboardForm;
use Modules\Ecommerce\Filament\Resources\Motherboards\Tables\MotherboardsTable;
use Modules\Ecommerce\Models\Motherboard;

class MotherboardResource extends Resource
{
    protected static ?string $model = Motherboard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MotherboardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MotherboardsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMotherboards::route('/'),
            'create' => CreateMotherboard::route('/create'),
            'edit' => EditMotherboard::route('/{record}/edit'),
        ];
    }
}
