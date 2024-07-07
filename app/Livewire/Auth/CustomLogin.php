<?php

namespace App\Livewire\Auth;

use Filament\Http\Livewire\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Helpers\PasswordManager;
use Illuminate\Support\Facades\Hash;

class CustomLogin extends BaseLogin
{
    protected function getFormSchema(): array
    {
        return [
            TextInput::make('email')
                ->label(__('Email Address'))
                ->required()
                ->email(),
            TextInput::make('password')
                ->label(__('Password'))
                ->required()
                ->password(),
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        $credentials = $this->form->getState();
        unset($credentials['remember']);

        $user = User::where('email', $credentials['email'])->first();
        if (!$user->new_pass || $user->new_pass == false){
            if (PasswordManager::verifyPassword($credentials['password'], $user->password)) {
                $user->new_pass = true;
                $user->password = Hash::make($credentials['password']);
                $user->save();
            }
        }
        if (Auth::guard('web')->validate($credentials)) {
            $user = Auth::guard('web')->getProvider()->retrieveByCredentials(['email'=> $credentials['email']]);
            Auth::guard('web')->login($user);
            return app(LoginResponse::class);
        }

        $this->addError('email', __('These credentials do not match our records.'));

        return null;
    }
}
