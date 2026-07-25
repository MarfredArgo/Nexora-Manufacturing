<?php

namespace Modules\Ecommerce\Filament\Widgets;

use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Modules\Ecommerce\Models\Order;

class LatestOrders extends TableWidget
{
    protected static ?string $heading = 'Latest Orders';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Order::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Order ID')
                    ->limit(12)
                    ->tooltip(fn ($record) => $record->id)
                    ->searchable(),

                TextColumn::make('user_id')
                    ->label('Customer ID')
                    ->limit(12),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'    => 'warning',
                        'processing' => 'info',
                        'shipped'    => 'primary',
                        'delivered'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'    => 'success',
                        'pending' => 'warning',
                        'failed'  => 'danger',
                        default   => 'gray',
                    }),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('PHP'),

                TextColumn::make('created_at')
                    ->label('Ordered At')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ]);
    }
}
