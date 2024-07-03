<?php

namespace App\Filament\Resources\OTPVerificationResource\Pages;

use App\Filament\Resources\OTPVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOTPVerifications extends ListRecords
{
    protected static string $resource = OTPVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
