<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Order;

class OrderWidgets extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = "full";

    public function table(Table $table): Table
    {
        return $table
        ->query(Order::query()->orderBy("created_at", 'DESC'))
        ->columns([
            Tables\Columns\TextColumn::make('order_id')->label('Order ID'),
            Tables\Columns\TextColumn::make('status')->label('Status')->color(fn (string $state): string => match ($state) {
                'PENDING' => 'gray',
                'PROCESSING_ORDER' => 'gray',
                'PENDING_RIDER_CONFIRMATION' => 'info',
                'RIDER_ACCEPTED_ORDER' => 'info',
                'RIDER_AT_PICK_UP' => 'info',
                'RIDER_PICKED_UP_ORDER' => 'info',
                'ORDER_ARRIVED' => 'success',
                'ORDER_DELIVERED' => 'success',
                'ORDER_COMPLETED' => 'success',
                'ORDER_CANCELLED' => 'warning',
                default => 'gray',
            }),
            Tables\Columns\TextColumn::make('created_at')->label('Date'),
            Tables\Columns\TextColumn::make('customer.display_name'),
            Tables\Columns\TextColumn::make('business.display_name'),
            Tables\Columns\TextColumn::make('rider.display_name'),

        ]);
    }
}
