<?php

namespace App\Filament\Resources\RiderCommissionResource\Pages;

use App\Filament\Resources\RiderCommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRiderCommissions extends ListRecords
{
    protected static string $resource = RiderCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
