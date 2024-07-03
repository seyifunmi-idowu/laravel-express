<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Helpers\PasswordManager;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Use your custom password manager to hash the password
        if (isset($data['password'])) {
            $data['password'] = PasswordManager::hashPassword($data['password']);
        }

        return static::getModel()::create($data);
    }
}
