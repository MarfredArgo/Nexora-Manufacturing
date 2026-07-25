<?php

namespace Modules\Ecommerce\Filament\Resources\Cpus;

use App\Models\Cpu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\Cpus\Pages\CreateCpu;
use Modules\Ecommerce\Filament\Resources\Cpus\Pages\EditCpu;
use Modules\Ecommerce\Filament\Resources\Cpus\Pages\ListCpus;
use Modules\Ecommerce\Filament\Resources\Cpus\Schemas\CpuForm;
use Modules\Ecommerce\Filament\Resources\Cpus\Tables\CpusTable;

class CpuResource extends Resource
{
    protected static ?string $model = Cpu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CpuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CpusTable::configure($table);
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
            'index' => ListCpus::route('/'),
            'create' => CreateCpu::route('/create'),
            'edit' => EditCpu::route('/{record}/edit'),
        ];
    }
}
