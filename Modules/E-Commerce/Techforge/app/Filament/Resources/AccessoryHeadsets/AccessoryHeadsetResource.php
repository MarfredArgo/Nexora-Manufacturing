<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryHeadsets;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\AccessoryHeadsets\Pages\CreateAccessoryHeadset;
use Modules\Ecommerce\Filament\Resources\AccessoryHeadsets\Pages\EditAccessoryHeadset;
use Modules\Ecommerce\Filament\Resources\AccessoryHeadsets\Pages\ListAccessoryHeadsets;
use Modules\Ecommerce\Filament\Resources\AccessoryHeadsets\Schemas\AccessoryHeadsetForm;
use Modules\Ecommerce\Filament\Resources\AccessoryHeadsets\Tables\AccessoryHeadsetsTable;
use Modules\Ecommerce\Models\AccessoryHeadset;

class AccessoryHeadsetResource extends Resource
{
    protected static ?string $model = AccessoryHeadset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AccessoryHeadsetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccessoryHeadsetsTable::configure($table);
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
            'index' => ListAccessoryHeadsets::route('/'),
            'create' => CreateAccessoryHeadset::route('/create'),
            'edit' => EditAccessoryHeadset::route('/{record}/edit'),
        ];
    }
}
