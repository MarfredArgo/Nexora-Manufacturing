<?php

namespace Modules\Ecommerce\Filament\Resources\PcCases;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\PcCases\Pages\CreatePcCase;
use Modules\Ecommerce\Filament\Resources\PcCases\Pages\EditPcCase;
use Modules\Ecommerce\Filament\Resources\PcCases\Pages\ListPcCases;
use Modules\Ecommerce\Filament\Resources\PcCases\Schemas\PcCaseForm;
use Modules\Ecommerce\Filament\Resources\PcCases\Tables\PcCasesTable;
use Modules\Ecommerce\Models\PcCase;

class PcCaseResource extends Resource
{
    protected static ?string $model = PcCase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PcCaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PcCasesTable::configure($table);
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
            'index' => ListPcCases::route('/'),
            'create' => CreatePcCase::route('/create'),
            'edit' => EditPcCase::route('/{record}/edit'),
        ];
    }
}
