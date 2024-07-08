<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\User;
use App\Models\Rider;
use App\Models\Customer;
use App\Models\Business;
use App\Models\Order;
use App\Models\Transaction;

class UserWidgets extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $thisMonthOrders = Order::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        $thisMonthOrders = Order::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $completedOrders = Order::where('status', 'ORDER_COMPLETED')
                                ->count();
        $ongoingOrders = Order::whereNotIn('status', ['ORDER_COMPLETED', 'ORDER_CANCELLED', 'ORDER_DELIVERED'])
                              ->count();
        $pendingOrders = Order::where('status', 'PENDING')
                              ->count();
        $cancelledOrders = Order::where('status', 'ORDER_CANCELLED')
                                ->count();


        return [
            Stat::make('Total Users', User::count()),
            Stat::make('Total Riders', Rider::count()),
            Stat::make('Total Customers', Customer::count()),
            Stat::make('Total Business', Business::count()),
            Stat::make('Total Transaction', Transaction::count()),
            Stat::make('Total Orders', Order::count()),
            Stat::make('This Month\'s Orders', $thisMonthOrders),
            Stat::make('Completed Orders', $completedOrders),
            Stat::make('Ongoing Orders', $ongoingOrders),
            Stat::make('Pending Orders', $pendingOrders),
            Stat::make('Cancelled Orders', $cancelledOrders),

            // Stat::make('Tasks Created Today', Task::whereDate('created_at', today())->count()),
        ];
    }
}
