<?php

namespace Modules\Ecommerce\Filament\Resources\Rams;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\Rams\Pages\CreateRam;
use Modules\Ecommerce\Filament\Resources\Rams\Pages\EditRam;
use Modules\Ecommerce\Filament\Resources\Rams\Pages\ListRams;
use Modules\Ecommerce\Filament\Resources\Rams\Schemas\RamForm;
use Modules\Ecommerce\Filament\Resources\Rams\Tables\RamsTable;
use Modules\Ecommerce\Models\Ram;

class RamResource extends Resource
{
    protected static ?string $model = Ram::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RamsTable::configure($table);
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
            'index' => ListRams::route('/'),
            'create' => CreateRam::route('/create'),
            'edit' => EditRam::route('/{record}/edit'),
        ];
    }
}
