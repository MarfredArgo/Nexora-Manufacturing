<?php

namespace Modules\Ecommerce\Filament\Resources\Storages;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\Storages\Pages\CreateStorage;
use Modules\Ecommerce\Filament\Resources\Storages\Pages\EditStorage;
use Modules\Ecommerce\Filament\Resources\Storages\Pages\ListStorages;
use Modules\Ecommerce\Filament\Resources\Storages\Schemas\StorageForm;
use Modules\Ecommerce\Filament\Resources\Storages\Tables\StoragesTable;
use Modules\Ecommerce\Models\Storage;

class StorageResource extends Resource
{
    protected static ?string $model = Storage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return StorageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StoragesTable::configure($table);
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
            'index' => ListStorages::route('/'),
            'create' => CreateStorage::route('/create'),
            'edit' => EditStorage::route('/{record}/edit'),
        ];
    }
}
