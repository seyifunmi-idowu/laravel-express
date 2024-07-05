<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Orders'),
            'Awaiting riders' => Tab::make('Awaiting riders')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('rider_id', null)),
            'business_orders' => Tab::make('Business orders')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('order_by', "BUSINESS")),
            'customer_orders' => Tab::make('Customer orders')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('order_by', "CUSTOMER")),

        ];
    }
}
