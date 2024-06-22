<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\User;
use App\Helpers\StaticFunction;

class WalletService
{
    public function __construct()
    {
    }

    public function createUserWallet(User $user)
    {
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0
        ]);
    }
}
