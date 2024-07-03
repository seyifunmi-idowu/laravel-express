<?php

namespace App\Filament\Resources\ReferralUserResource\Pages;

use App\Filament\Resources\ReferralUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReferralUsers extends ListRecords
{
    protected static string $resource = ReferralUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
