<?php
namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Form;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \Filament\Widgets\AccountWidget::class,
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('first_name')
                ->label('First Name')
                ->disabled(),
            Forms\Components\TextInput::make('last_name')
                ->label('Last Name')
                ->disabled(),
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->disabled(),
            Forms\Components\TextInput::make('phone_number')
                ->label('Phone')
                ->disabled(),
            Forms\Components\Toggle::make('is_superuser')
                ->label('Is Superuser')
                ->disabled(),
            Forms\Components\Toggle::make('email_verified')
                ->label('Email Verification Status')
                ->disabled(),
            Placeholder::make('avatar_image')
                ->label('Avatar Image')
                ->content(fn ($record) => $record && $record->avatar_url ? '<img src="' . $record->avatar_url . '" style="max-width: 150px; border-radius: 50%;" />' : 'No image available'),
        ];
    }
}
