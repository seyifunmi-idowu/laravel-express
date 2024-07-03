<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Customer'),
            'individual' => Tab::make('Individual')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('customer_type', "INDIVIDUAL")),
            'business' => Tab::make('Business')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('customer_type', "BUSINESS")),
        ];
    }
}
