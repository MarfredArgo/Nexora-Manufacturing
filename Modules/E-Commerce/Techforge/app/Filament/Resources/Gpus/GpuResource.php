<?php

namespace Modules\Ecommerce\Filament\Resources\Gpus;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\Gpus\Pages\CreateGpu;
use Modules\Ecommerce\Filament\Resources\Gpus\Pages\EditGpu;
use Modules\Ecommerce\Filament\Resources\Gpus\Pages\ListGpus;
use Modules\Ecommerce\Filament\Resources\Gpus\Schemas\GpuForm;
use Modules\Ecommerce\Filament\Resources\Gpus\Tables\GpusTable;
use Modules\Ecommerce\Models\Gpu;

class GpuResource extends Resource
{
    protected static ?string $model = Gpu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return GpuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GpusTable::configure($table);
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
            'index' => ListGpus::route('/'),
            'create' => CreateGpu::route('/create'),
            'edit' => EditGpu::route('/{record}/edit'),
        ];
    }
}
