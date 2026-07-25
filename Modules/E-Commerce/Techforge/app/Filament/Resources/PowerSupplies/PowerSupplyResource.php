<?php

namespace Modules\Ecommerce\Filament\Resources\PowerSupplies;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\PowerSupplies\Pages\CreatePowerSupply;
use Modules\Ecommerce\Filament\Resources\PowerSupplies\Pages\EditPowerSupply;
use Modules\Ecommerce\Filament\Resources\PowerSupplies\Pages\ListPowerSupplies;
use Modules\Ecommerce\Filament\Resources\PowerSupplies\Schemas\PowerSupplyForm;
use Modules\Ecommerce\Filament\Resources\PowerSupplies\Tables\PowerSuppliesTable;
use Modules\Ecommerce\Models\PowerSupply;

class PowerSupplyResource extends Resource
{
    protected static ?string $model = PowerSupply::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PowerSupplyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PowerSuppliesTable::configure($table);
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
            'index' => ListPowerSupplies::route('/'),
            'create' => CreatePowerSupply::route('/create'),
            'edit' => EditPowerSupply::route('/{record}/edit'),
        ];
    }
}
