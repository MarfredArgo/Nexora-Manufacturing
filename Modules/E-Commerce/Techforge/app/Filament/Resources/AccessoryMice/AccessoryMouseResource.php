<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryMice;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\AccessoryMice\Pages\CreateAccessoryMouse;
use Modules\Ecommerce\Filament\Resources\AccessoryMice\Pages\EditAccessoryMouse;
use Modules\Ecommerce\Filament\Resources\AccessoryMice\Pages\ListAccessoryMice;
use Modules\Ecommerce\Filament\Resources\AccessoryMice\Schemas\AccessoryMouseForm;
use Modules\Ecommerce\Filament\Resources\AccessoryMice\Tables\AccessoryMiceTable;
use Modules\Ecommerce\Models\AccessoryMouse;

class AccessoryMouseResource extends Resource
{
    protected static ?string $model = AccessoryMouse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AccessoryMouseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccessoryMiceTable::configure($table);
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
            'index' => ListAccessoryMice::route('/'),
            'create' => CreateAccessoryMouse::route('/create'),
            'edit' => EditAccessoryMouse::route('/{record}/edit'),
        ];
    }
}
