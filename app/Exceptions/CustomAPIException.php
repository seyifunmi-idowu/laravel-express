<?php

namespace App\Exceptions;
use App\Helpers\ApiResponse;

use Exception;
use Illuminate\Http\JsonResponse;

class CustomAPIException extends Exception
{
    protected $statusCode;
    protected $defaultMessage = 'We are unable to process your request at this time. Please try again.';

    public function __construct($message = null, $statusCode = 400)
    {
        parent::__construct($message ?? $this->defaultMessage);
        $this->statusCode = $statusCode;
    }

    public function render(): JsonResponse
    {
        return ApiResponse::responseError([], $this->getMessage(), $this->statusCode);
    }
}
