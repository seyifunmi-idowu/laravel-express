<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\User;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Helpers\Validator;
use Carbon\Carbon;
use App\Exceptions\CustomAPIException;
use PhpParser\Node\Expr\FuncCall;

class WalletService
{
    protected PaystackService $paystackService;

    public function __construct(
        PaystackService $paystackService
    )
    {
        $this->paystackService = $paystackService;
    }

    public function createUserWallet(User $user)
    {
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0
        ]);
    }

    public function getUserBanks($user)
    {
        return BankAccount::where(['user_id'=> $user->id, 'save_account' => true])->get();
    }

    public static function getUserTransaction($request)
    {
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');
        $transactionStatus = strtoupper($request->input('transaction_status', ''));
        $transactionType = strtoupper($request->input('transaction_type', ''));

        // Get all transactions for the user
        $transactions = Transaction::where('user_id', $request->user()->id)-> orderBy('created_at', 'desc');

        if (!empty($startDate) && !empty($endDate)) {
            Validator::isStartDateLessThanOrEqualsEndDate($startDate, $endDate);
            $startDateTime = Carbon::parse($startDate);
            $endDateTime = Carbon::parse($endDate)->addDay(); // Add one day to include end date
            $transactions->whereBetween('created_at', [$startDateTime, $endDateTime]);
        }

        if (!empty($transactionStatus)) {
            $transactions->where('transaction_status', $transactionStatus);
        }

        if (!empty($transactionType)) {
            $transactions->where('transaction_type', $transactionType);
        }

        return $transactions;
    }

    public function getBankNameWithBankCode($bank_code)
    {
        $banks = $this->paystackService->getBanks();
        $bank_details = collect($banks)->firstWhere('code', $bank_code);
        return $bank_details['name'] ?? null;
    }

    public function getOrCreateTransferRecipient($user, $bank_code, $account_number, $save = false)
    {
        $user_bank = BankAccount::where('user_id', $user->id)
            ->where('account_number', $account_number)
            ->where('bank_code', $bank_code)
            ->first();

        if ($user_bank) {
            return $user_bank;
        }
        
        $verify_account = $this->paystackService->verifyAccountNumber($bank_code, $account_number);
        if (!$verify_account['status']){
            throw new CustomAPIException("Unable to verify account number", 422);
        }

        $account_name = $verify_account['data']['account_name'];

        $createRecipientResponse = $this->paystackService->createTransferRecipient($account_name, $bank_code, $account_number);

        if (!$createRecipientResponse['status']) {
            throw new CustomAPIException(
                'Error occurred, unable to transfer',
                422
            );
        }

        $recipient_code = $createRecipientResponse['data']['recipient_code'];
        $bank_name = $this->getBankNameWithBankCode($bank_code);

        $bankAccount = BankAccount::create([
            'user_id' => $user->id,
            'account_number' => $account_number,
            'account_name' => $account_name,
            'bank_code' => $bank_code,
            'bank_name' => $bank_name,
            'recipient_code' => $recipient_code,
            'meta' => $createRecipientResponse,
            'save_account' => $save,
        ]);

        return $bankAccount;
    }

    public function withdrawFromWallet(User $user, float $amount, string $recipient_code)
    {
        $paystack_response = $this->paystackService->initiateTransfer($amount, $recipient_code);

        $userWallet = $user->wallet;
        $userWallet->withdraw($amount);

        $reference = $paystack_response['data']['reference'];
        $transaction_obj = Transaction::create([
            'transaction_type' => 'DEBIT',
            'transaction_status' => 'SUCCESS',
            'amount' => $amount,
            'user_id' => $user->id,
            'reference' => $reference,
            'pssp' => 'PAYSTACK',
            'payment_category' => 'WITHDRAW',
            'pssp_meta_data' => $paystack_response['data'],
            'currency' => "₦",
        ]);

        return [
            'amount' => $transaction_obj->amount,
            'created_at' => $transaction_obj->created_at,
            'reference' => $transaction_obj->reference,
        ];
    } 

    public function transferToBank($user, $request)
    {
        $amount = $request->input('amount', '');
        $account_number = $request->input('account_number', '');
        $bank_code = $request->input('bank_code', '');
        $save_account = $request->input('save_account', false);

        $user_wallet = $user->wallet;
        if ($user_wallet->balance < $amount)
        {
            throw new CustomAPIException("Insufficient balance", 400);
        }

        $bank_account = $this->getOrCreateTransferRecipient($user, $bank_code, $account_number, $save_account);
        $transaction_data = $this->withdrawFromWallet($user, $amount, $bank_account->recipient_code);
        $response = [
            'bank_name' => $bank_account->account_name,
            'account_number' => $bank_account->account_number,
            'account_name' => $bank_account->account_name,
            ...$transaction_data,
        ];
        
        return $response;
    }

    public function transferToBeneficiary($user, $request)
    {
        $amount = $request->input('amount');
        $beneficiary_id = $request->input('beneficiary_id', '');

        $user_wallet = $user->wallet;
        if ($user_wallet->balance < $amount)
        {
            throw new CustomAPIException("Insufficient balance", 400);
        }

        $bank_account = BankAccount::where('id', $beneficiary_id)->first();
        if (!$bank_account) {
            throw new CustomAPIException("Beneficiary not found", 400);
        }
        $transaction_data = $this->withdrawFromWallet($user, $amount, $bank_account->recipient_code);
        $response = [
            'bank_name' => $bank_account->account_name,
            'account_number' => $bank_account->account_number,
            'account_name' => $bank_account->account_name,
            ...$transaction_data,
        ];
        return $response;
    }

}
