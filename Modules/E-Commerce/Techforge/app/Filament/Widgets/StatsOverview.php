<?php

namespace Modules\Ecommerce\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Ecommerce\Models\Order;
use Modules\Ecommerce\Models\User;
use Modules\Ecommerce\Models\Cpu;
use Modules\Ecommerce\Models\Gpu;
use Modules\Ecommerce\Models\Laptop;
use Modules\Ecommerce\Models\Concerns\BelongsToClient;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::query()
            ->where('payment_status', 'paid')
            ->sum('total');
        $totalCustomers = User::count();
        $pendingOrders = Order::query()
            ->where('status', 'pending')
            ->count();

        return [
            Stat::make('Total Orders', number_format($totalOrders))
                ->description('All time orders')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('Total Revenue', '₱' . number_format($totalRevenue, 2))
                ->description('From paid orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Total Customers', number_format($totalCustomers))
                ->description('Registered storefront users')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Pending Orders', number_format($pendingOrders))
                ->description('Awaiting fulfillment')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success'),
        ];
    }
}
