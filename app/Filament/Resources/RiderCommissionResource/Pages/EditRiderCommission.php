<?php

namespace App\Filament\Resources\RiderCommissionResource\Pages;

use App\Filament\Resources\RiderCommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRiderCommission extends EditRecord
{
    protected static string $resource = RiderCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
