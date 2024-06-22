<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignUserRoles extends Command
{
    protected $signature = 'assign:user-roles';
    protected $description = 'Assign roles to users based on their user_type';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $roles = ['customer', 'business', 'rider', 'admin'];

        // Ensure all roles exist
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Fetch all users and assign roles based on user_type
        $users = User::all();
        foreach ($users as $user) {
            if ($user->user_type) {
                $role = strtolower($user->user_type);
                if (in_array($role, $roles)) {
                    $user->assignRole($role);
                    $this->info("Assigned role '{$role}' to user '{$user->email}'");
                } else {
                    $this->warn("User '{$user->email}' has an invalid user_type '{$user->user_type}'");
                }
            } else {
                $this->warn("User '{$user->email}' has an invalid or missing user_type");
            }
        }

        $this->info('User role assignment completed.');
    }
}
