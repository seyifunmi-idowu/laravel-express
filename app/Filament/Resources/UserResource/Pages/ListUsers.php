<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All users'),
            'customer' => Tab::make('Customer')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_type', "CUSTOMER")),
            'business' => Tab::make('Business')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_type', "BUSINESS")),
            'rider' => Tab::make('Rider')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_type', "RIDER")),
            'admin' => Tab::make('Admin')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_type', "ADMIN")),

        ];
    }
}
