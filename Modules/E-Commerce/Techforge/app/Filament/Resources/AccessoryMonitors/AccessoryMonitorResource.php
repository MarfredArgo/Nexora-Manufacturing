<?php

namespace Modules\Ecommerce\Filament\Resources\AccessoryMonitors;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\AccessoryMonitors\Pages\CreateAccessoryMonitor;
use Modules\Ecommerce\Filament\Resources\AccessoryMonitors\Pages\EditAccessoryMonitor;
use Modules\Ecommerce\Filament\Resources\AccessoryMonitors\Pages\ListAccessoryMonitors;
use Modules\Ecommerce\Filament\Resources\AccessoryMonitors\Schemas\AccessoryMonitorForm;
use Modules\Ecommerce\Filament\Resources\AccessoryMonitors\Tables\AccessoryMonitorsTable;
use Modules\Ecommerce\Models\AccessoryMonitor;

class AccessoryMonitorResource extends Resource
{
    protected static ?string $model = AccessoryMonitor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AccessoryMonitorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccessoryMonitorsTable::configure($table);
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
            'index' => ListAccessoryMonitors::route('/'),
            'create' => CreateAccessoryMonitor::route('/create'),
            'edit' => EditAccessoryMonitor::route('/{record}/edit'),
        ];
    }
}
