<?php

namespace Modules\Ecommerce\Filament\Resources\CustombuiltConfigs;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\CustombuiltConfigs\Pages\CreateCustombuiltConfig;
use Modules\Ecommerce\Filament\Resources\CustombuiltConfigs\Pages\EditCustombuiltConfig;
use Modules\Ecommerce\Filament\Resources\CustombuiltConfigs\Pages\ListCustombuiltConfigs;
use Modules\Ecommerce\Filament\Resources\CustombuiltConfigs\Schemas\CustombuiltConfigForm;
use Modules\Ecommerce\Filament\Resources\CustombuiltConfigs\Tables\CustombuiltConfigsTable;
use Modules\Ecommerce\Models\CustombuiltConfig;

class CustombuiltConfigResource extends Resource
{
    protected static ?string $model = CustombuiltConfig::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CustombuiltConfigForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustombuiltConfigsTable::configure($table);
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
            'index' => ListCustombuiltConfigs::route('/'),
            'create' => CreateCustombuiltConfig::route('/create'),
            'edit' => EditCustombuiltConfig::route('/{record}/edit'),
        ];
    }
}
