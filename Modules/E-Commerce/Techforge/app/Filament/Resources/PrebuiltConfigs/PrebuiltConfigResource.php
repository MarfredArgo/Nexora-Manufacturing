<?php

namespace Modules\Ecommerce\Filament\Resources\PrebuiltConfigs;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\PrebuiltConfigs\Pages\CreatePrebuiltConfig;
use Modules\Ecommerce\Filament\Resources\PrebuiltConfigs\Pages\EditPrebuiltConfig;
use Modules\Ecommerce\Filament\Resources\PrebuiltConfigs\Pages\ListPrebuiltConfigs;
use Modules\Ecommerce\Filament\Resources\PrebuiltConfigs\Schemas\PrebuiltConfigForm;
use Modules\Ecommerce\Filament\Resources\PrebuiltConfigs\Tables\PrebuiltConfigsTable;
use Modules\Ecommerce\Models\PrebuiltConfig;

class PrebuiltConfigResource extends Resource
{
    protected static ?string $model = PrebuiltConfig::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PrebuiltConfigForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrebuiltConfigsTable::configure($table);
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
            'index' => ListPrebuiltConfigs::route('/'),
            'create' => CreatePrebuiltConfig::route('/create'),
            'edit' => EditPrebuiltConfig::route('/{record}/edit'),
        ];
    }
}
