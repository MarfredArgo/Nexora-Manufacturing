<?php

namespace Modules\Ecommerce\Filament\Resources\Orders;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Ecommerce\Filament\Resources\Orders\Pages\CreateOrder;
use Modules\Ecommerce\Filament\Resources\Orders\Pages\EditOrder;
use Modules\Ecommerce\Filament\Resources\Orders\Pages\ListOrders;
use Modules\Ecommerce\Filament\Resources\Orders\Schemas\OrderForm;
use Modules\Ecommerce\Filament\Resources\Orders\Tables\OrdersTable;
use Modules\Ecommerce\Models\Order;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Storefront Orders';

    protected static \UnitEnum|string|null $navigationGroup = 'Storefront';

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
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
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
