<?php

namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Console\Command;

class MakeCustomFilamentUser extends Command
{
    protected $signature = 'make:custom-filament-user';
    protected $description = 'Create a new Filament user with custom fields';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $firstName = $this->ask('What is the first name of the user?');
        $email = $this->ask('What is the email of the user?');
        $password = $this->secret('What is the password of the user?');

        $user = User::create([
            'first_name' => $firstName,
            'email' => $email,
            'password' => Hash::make($password),
            'is_superuser' => true, // Set is_superuser to true
        ]);

        $this->info("User {$user->first_name} created successfully.");
    }
}
