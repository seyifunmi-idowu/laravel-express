<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\RequestException;

class PaystackService
{
    protected $client;
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->secretKey = config('constants.PAYSTACK_SECRET_KEY');
        $this->baseUrl = 'https://api.paystack.co/';
    }

    public function verifyAccountNumber($bankCode, $accountNumber)
    {
        $url = $this->baseUrl . "bank/resolve?account_number={$accountNumber}&bank_code={$bankCode}";
        try {
            $response = $this->client->get($url, [
                'headers' => $this->getHeaders(),
            ]);

            return json_decode($response->getBody(), true);

        } catch (RequestException $e) {
            return [
                'status' => false,
                'message' => $e->getResponse()->getBody()->getContents(),
            ];
        }
    }

    public function getBanks()
    {
        $url = $this->baseUrl . "bank";
        $response = $this->client->get($url, [
            'headers' => $this->getHeaders(),
        ]);

        $banks = json_decode($response->getBody(), true)["data"];

        // Cache the bank list for 24 hours to optimize performance
        // Cache::put('banks', $banks, 1440);

        return $this->formatListOfBanks($banks);
    }

    public function createTransferRecipient($name, $bankCode, $accountNumber)
    {
        $url = $this->baseUrl . "transferrecipient";
        $data = [
            "type" => "nuban",
            "name" => $name,
            "account_number" => $accountNumber,
            "bank_code" => $bankCode,
            "currency" => "NGN",
        ];

        $response = $this->client->post($url, [
            'headers' => $this->getHeaders(),
            'json' => $data,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function initiateTransfer($amount, $recipient)
    {
        $url = $this->baseUrl . "transfer";
        $data = [
            "source" => "balance",
            "amount" => $amount * 100,
            "recipient" => $recipient,
            "reason" => "payment from fele",
        ];

        $response = $this->client->post($url, [
            'headers' => $this->getHeaders(),
            'json' => $data,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function createPaymentPage($data)
    {
        $url = $this->baseUrl . "page";
        $response = $this->client->post($url, [
            'headers' => $this->getHeaders(),
            'json' => $data,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function verifyTransaction($reference)
    {
        $url = $this->baseUrl . "transaction/verify/{$reference}";
        $response = $this->client->get($url, [
            'headers' => $this->getHeaders(),
        ]);

        return json_decode($response->getBody(), true);
    }

    public function initializePayment($email, $amount, $currency = "NGN", $callbackUrl = null)
    {
        $url = $this->baseUrl . "transaction/initialize";
        $data = [
            "email" => $email,
            "amount" => $amount * 100,
            "currency" => $currency,
            "callback_url" => $callbackUrl,
        ];

        $response = $this->client->post($url, [
            'headers' => $this->getHeaders(),
            'json' => $data,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function chargeCard($email, $amount, $cardAuth)
    {
        $url = $this->baseUrl . "transaction/charge_authorization";
        $data = [
            "email" => $email,
            "amount" => round($amount * 100),
            "authorization_code" => $cardAuth,
        ];

        $response = $this->client->post($url, [
            'headers' => $this->getHeaders(),
            'json' => $data,
        ]);

        return json_decode($response->getBody(), true);
    }

    private function getHeaders()
    {
        return [
            "Authorization" => "Bearer {$this->secretKey}",
            "Content-Type" => "application/json",
        ];
    }

    private function formatListOfBanks($banksData)
    {
        return array_map(function ($bank) {
            return [
                'name' => $bank['name'],
                'code' => $bank['code'],
            ];
        }, $banksData);
    }
}
