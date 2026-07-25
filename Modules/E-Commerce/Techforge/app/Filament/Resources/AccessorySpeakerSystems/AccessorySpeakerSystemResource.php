<?php

namespace Modules\Ecommerce\Filament\Resources\AccessorySpeakerSystems;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\AccessorySpeakerSystems\Pages\CreateAccessorySpeakerSystem;
use Modules\Ecommerce\Filament\Resources\AccessorySpeakerSystems\Pages\EditAccessorySpeakerSystem;
use Modules\Ecommerce\Filament\Resources\AccessorySpeakerSystems\Pages\ListAccessorySpeakerSystems;
use Modules\Ecommerce\Filament\Resources\AccessorySpeakerSystems\Schemas\AccessorySpeakerSystemForm;
use Modules\Ecommerce\Filament\Resources\AccessorySpeakerSystems\Tables\AccessorySpeakerSystemsTable;
use Modules\Ecommerce\Models\AccessorySpeakerSystem;

class AccessorySpeakerSystemResource extends Resource
{
    protected static ?string $model = AccessorySpeakerSystem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AccessorySpeakerSystemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AccessorySpeakerSystemsTable::configure($table);
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
            'index' => ListAccessorySpeakerSystems::route('/'),
            'create' => CreateAccessorySpeakerSystem::route('/create'),
            'edit' => EditAccessorySpeakerSystem::route('/{record}/edit'),
        ];
    }
}
