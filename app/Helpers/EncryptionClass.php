<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Config;

class EncryptionClass
{
    protected static $encryptionKey;

    public static function initializeEncryptionKey()
    {
        if (!self::$encryptionKey) {
            $encryptionKey = Config::get('constants.ENCRYPTION_KEY');
            // Decode the base64-encoded key
            self::$encryptionKey = base64_decode($encryptionKey);
        }
    }

    public static function encryptData($data)
    {
        self::initializeEncryptionKey();
        try {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encryptedData = openssl_encrypt($data, 'aes-256-cbc', self::$encryptionKey, 0, $iv);
            $encryptedData = base64_encode($encryptedData . '::' . $iv);
            return $encryptedData;
        } catch (Exception $e) {
            // Handle encryption exception
            return null;
        }
    }

    public static function decryptData($encryptedData)
    {
        self::initializeEncryptionKey();
        try {
            list($encryptedData, $iv) = explode('::', base64_decode($encryptedData), 2);
            $decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', self::$encryptionKey, 0, $iv);
            return $decryptedData;
        } catch (Exception $e) {
            // Handle decryption exception
            return null;
        }
    }
}
