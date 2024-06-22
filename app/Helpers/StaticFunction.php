<?php

namespace App\Helpers;

class StaticFunction
{
    public static function generate_id()
    {
        $data = random_bytes(16);
        assert(strlen($data) == 16);
    
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        return bin2hex($data);
    }

    public static function generateCode(int $length = 8): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $referralCode = '';
        for ($i = 0; $i < $length; $i++) {
            $referralCode .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $referralCode;
    }
}

