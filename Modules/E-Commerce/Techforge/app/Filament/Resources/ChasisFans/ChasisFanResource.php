<?php

namespace Modules\Ecommerce\Filament\Resources\ChasisFans;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\ChasisFans\Pages\CreateChasisFan;
use Modules\Ecommerce\Filament\Resources\ChasisFans\Pages\EditChasisFan;
use Modules\Ecommerce\Filament\Resources\ChasisFans\Pages\ListChasisFans;
use Modules\Ecommerce\Filament\Resources\ChasisFans\Schemas\ChasisFanForm;
use Modules\Ecommerce\Filament\Resources\ChasisFans\Tables\ChasisFansTable;
use Modules\Ecommerce\Models\ChasisFan;

class ChasisFanResource extends Resource
{
    protected static ?string $model = ChasisFan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ChasisFanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChasisFansTable::configure($table);
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
            'index' => ListChasisFans::route('/'),
            'create' => CreateChasisFan::route('/create'),
            'edit' => EditChasisFan::route('/{record}/edit'),
        ];
    }
}
