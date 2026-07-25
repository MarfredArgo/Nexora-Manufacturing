<?php

namespace Modules\Ecommerce\Filament\Resources\Coolers;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\Coolers\Pages\CreateCooler;
use Modules\Ecommerce\Filament\Resources\Coolers\Pages\EditCooler;
use Modules\Ecommerce\Filament\Resources\Coolers\Pages\ListCoolers;
use Modules\Ecommerce\Filament\Resources\Coolers\Schemas\CoolerForm;
use Modules\Ecommerce\Filament\Resources\Coolers\Tables\CoolersTable;
use Modules\Ecommerce\Models\Cooler;

class CoolerResource extends Resource
{
    protected static ?string $model = Cooler::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CoolerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoolersTable::configure($table);
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
            'index' => ListCoolers::route('/'),
            'create' => CreateCooler::route('/create'),
            'edit' => EditCooler::route('/{record}/edit'),
        ];
    }
}
