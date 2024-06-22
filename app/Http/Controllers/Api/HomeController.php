<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {   
        return ApiResponse::responseSuccess([], 'Fele express API');
    }
}
