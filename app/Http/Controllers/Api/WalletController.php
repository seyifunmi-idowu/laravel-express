<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Resources\VehicleResource;
use App\Http\Controllers\Controller;
use App\Services\WalletService;
use App\Services\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;
use Illuminate\Validation\ValidationException;
use App\Exceptions\CustomAPIException;
use Illuminate\Support\Facades\Response;

class WalletController extends Controller
{
    protected WalletService $walletService;
    protected PaystackService $paystackService;

    public function __construct(
        WalletService $walletService,
        PaystackService $paystackService
    )
    {
        $this->walletService = $walletService;
        $this->paystackService = $paystackService;
    }

    public function getListOfBank(): JsonResponse
    {
        $response = $this->paystackService->getBanks();
        return ApiResponse::responseSuccess($response, 'List of banks');
    }
    
    public function getUserBeneficiary(Request $request): JsonResponse
    {
        $response = $this->walletService->getUserBanks($request->user());
        return ApiResponse::responseSuccess($response, 'List of banks');
    }
    
    public function verifyAccountNumber(Request $request): JsonResponse
    {
        try{
            $request->validate([
                'bank_code' => 'required',
                'account_number' => 'required|min:10|max:10',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        
        $response = $this->paystackService->verifyAccountNumber($request->bank_code, $request->account_number);
        if (!$response['status']){
            throw new CustomAPIException("Unable to verify account number", 422);
        }
        return ApiResponse::responseSuccess(["account_name" => $response["data"]["account_name"]], 'success');
    }

    public function transferToBank(Request $request): JsonResponse
    {
        try{
            $request->validate([
                'bank_code' => 'required',
                'account_number' => 'required|min:10|max:10',
                'amount' => 'required|int',
                'save_account' => 'nullable|bool',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        
        $response = $this->walletService->transferToBank($request->user(), $request);
        return ApiResponse::responseSuccess($response, 'Transfer in progress');
    }

    public function transferToBeneficiary(Request $request): JsonResponse
    {
        try{
            $request->validate([
                'beneficiary_id' => 'required',
                'amount' => 'required|int',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        
        $response = $this->walletService->transferToBeneficiary($request->user(), $request);
        return ApiResponse::responseSuccess($response, 'Transfer in progress');
    }

    public function getWalletalance(Request $request): JsonResponse
    {
        $wallet_balance = $request->user()->wallet->balance;
        return ApiResponse::responseSuccess(["balance" => $wallet_balance], 'Wallet balance');
    }

    public function getUserTransaction(Request $request): JsonResponse
    {
        $transactionsQuery = $this->walletService->getUserTransaction($request);

        $perPage = $request->query('per_page', 10);
        $transactions = $transactionsQuery->paginate($perPage);

        $formattedTransactions = [
            'count' => $transactions->total(),
            'total_pages' => $transactions->lastPage(),
            'current_page' => $transactions->currentPage(),
            'data' => $transactions->items(), 
        ];

        return Response::json($formattedTransactions);
    }

    public function getUserCards(Request $request): JsonResponse
    {
        $response = $this->walletService->getUserCards($request->user());
        return ApiResponse::responseSuccess($response, 'User cards');
    }

    public function initiateCardTransaction(Request $request): JsonResponse
    {
        try{
            $request->validate([
                'amount' => 'required',
            ]);
        } catch (ValidationException $e) {
            $errors =$e->errors();
            $message  = collect($errors)->unique()->first();
            return ApiResponse::responseValidateError($errors,  $message[0]);
        }
        
        $response = $this->walletService->initiateCardTransaction($request->user(), $request->amount);
        return ApiResponse::responseSuccess($response, 'Card transaction initiated');
    }

    public function paystackCallback(Request $request): JsonResponse
    {
        $response = $this->walletService->verifyCardTransaction($request->query());
        return ApiResponse::responseSuccess($response, 'Transaction successful');
    }

    public function paystackWebhook(Request $request)
    {
        $requestData = $request->all();
        $event = $requestData['event'] ?? '';

        if ($event === 'charge.success') {
            $data = ['trxref' => $requestData['data']['reference'] ?? ''];

            try {
                $this->walletService->verifyCardTransaction($data);
            } catch (CustomAPIException $e) {
                return ApiResponse::responseSuccess([], $e->getMessage());
            }
        }

        return ApiResponse::responseSuccess([], 'Webhook successful');
    }
}
