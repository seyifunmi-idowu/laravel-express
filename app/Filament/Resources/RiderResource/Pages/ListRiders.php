<?php

namespace App\Filament\Resources\RiderResource\Pages;

use App\Filament\Resources\RiderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListRiders extends ListRecords
{
    protected static string $resource = RiderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Riders'),
            'approved_riders' => Tab::make('Approved riders')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', "APPROVED")),
            'pending_riders' => Tab::make('Pending riders')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotIn('status', ["APPROVED"])),

        ];
    }
}
