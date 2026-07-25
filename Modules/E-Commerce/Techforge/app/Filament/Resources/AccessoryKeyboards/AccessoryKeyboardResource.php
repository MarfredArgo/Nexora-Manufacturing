<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryKeyboards;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\AccessoryKeyboards\Pages\CreateAccessoryKeyboard;
use Modules\Ecommerce\Filament\Resources\AccessoryKeyboards\Pages\EditAccessoryKeyboard;
use Modules\Ecommerce\Filament\Resources\AccessoryKeyboards\Pages\ListAccessoryKeyboards;
use Modules\Ecommerce\Filament\Resources\AccessoryKeyboards\Schemas\AccessoryKeyboardForm;
use Modules\Ecommerce\Filament\Resources\AccessoryKeyboards\Tables\AccessoryKeyboardsTable;
use Modules\Ecommerce\Models\AccessoryKeyboard;

class AccessoryKeyboardResource extends Resource
{
    protected static ?string $model = AccessoryKeyboard::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AccessoryKeyboardForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccessoryKeyboardsTable::configure($table);
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
            'index' => ListAccessoryKeyboards::route('/'),
            'create' => CreateAccessoryKeyboard::route('/create'),
            'edit' => EditAccessoryKeyboard::route('/{record}/edit'),
        ];
    }
}
