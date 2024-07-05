<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\User;
use App\Models\Rider;
use App\Models\Customer;
use App\Models\Business;

class UserWidgets extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count()),
            Stat::make('Total Riders', Rider::count()),
            Stat::make('Total Customers', Customer::count()),
            Stat::make('Total Business', Business::count()),
            // Stat::make('Tasks Created Today', Task::whereDate('created_at', today())->count()),
        ];
    }
}
