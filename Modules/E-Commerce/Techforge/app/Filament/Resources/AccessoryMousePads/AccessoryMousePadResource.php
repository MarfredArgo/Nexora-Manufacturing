<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryMousePads;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\AccessoryMousePads\Pages\CreateAccessoryMousePad;
use Modules\Ecommerce\Filament\Resources\AccessoryMousePads\Pages\EditAccessoryMousePad;
use Modules\Ecommerce\Filament\Resources\AccessoryMousePads\Pages\ListAccessoryMousePads;
use Modules\Ecommerce\Filament\Resources\AccessoryMousePads\Schemas\AccessoryMousePadForm;
use Modules\Ecommerce\Filament\Resources\AccessoryMousePads\Tables\AccessoryMousePadsTable;
use Modules\Ecommerce\Models\AccessoryMousePad;

class AccessoryMousePadResource extends Resource
{
    protected static ?string $model = AccessoryMousePad::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AccessoryMousePadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccessoryMousePadsTable::configure($table);
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
            'index' => ListAccessoryMousePads::route('/'),
            'create' => CreateAccessoryMousePad::route('/create'),
            'edit' => EditAccessoryMousePad::route('/{record}/edit'),
        ];
    }
}
