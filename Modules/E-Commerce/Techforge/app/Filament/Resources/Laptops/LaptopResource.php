<?php

namespace Modules\Ecommerce\Filament\Resources\Laptops;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\Laptops\Pages\CreateLaptop;
use Modules\Ecommerce\Filament\Resources\Laptops\Pages\EditLaptop;
use Modules\Ecommerce\Filament\Resources\Laptops\Pages\ListLaptops;
use Modules\Ecommerce\Filament\Resources\Laptops\Schemas\LaptopForm;
use Modules\Ecommerce\Filament\Resources\Laptops\Tables\LaptopsTable;
use Modules\Ecommerce\Models\Laptop;

class LaptopResource extends Resource
{
    protected static ?string $model = Laptop::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LaptopForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LaptopsTable::configure($table);
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
            'index' => ListLaptops::route('/'),
            'create' => CreateLaptop::route('/create'),
            'edit' => EditLaptop::route('/{record}/edit'),
        ];
    }
}
