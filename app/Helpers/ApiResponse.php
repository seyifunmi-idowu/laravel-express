<?php

namespace App\Helpers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;
use Illuminate\Http\Request;

class ApiResponse
{
    protected static function respond(
        $data = [],
        string $status = 'success',
        string $message = null,
        int $statusCode = 200,
        array $options = []
    ): JsonResponse {

        if ($data instanceof JsonResource) {
            $data = $data->response()->getData(true);
            $response = array_merge([
                'status' => $status,
                'message' => $message,
            ], $data);
        } else{
            $response = [
                'status' => $status,
                'data' => $data,
                'message' => $message,
            ];
        }
        if (count($options) > 0) {
            foreach ($options as $key => $value) {
                $response[$key] = $value;
            }
        }

        return response()->json($response, $statusCode);
    }
    public static function responseSuccess(
        $data = [],
        string $message = "Success",
        array $options = []
    ): JsonResponse {
        return self::respond($data, 'success', $message, 200, $options);
    }

    public static function responseCreated(
        array $data = [],
        string $message = "New entity created",
        array $options = []
    ): JsonResponse {
        return self::respond($data, 'success', $message, 201, $options);
    }

    public static function responseNoContent(
        array $data = [],
        string $message = "No content",
        array $options = []
    ): JsonResponse {
        return self::respond($data, 'success', $message, 204, $options);
    }

    public static function responseError(
        array $data = [],
        string $message = "Error encountered",
        int $statusCode = 400,
        array $options = []
    ): JsonResponse {
        return self::respond($data, 'error', $message, $statusCode, $options);
    }

    public static function responseUnauthorized(string $message = 'Token is invalid or expired', array $options = []): JsonResponse
    {
        return self::responseError([], $message, 401, $options);
    }

    public static function responseForbidden(string $message = 'Forbidden', array $options = []): JsonResponse
    {
        return self::responseError([], $message, 403, $options);
    }

    public static function responseValidationError(Validator $validator, string $message = null): JsonResponse
    {
        $data = $validator->errors()->all();
        $error = collect($data)->unique()->first();
        $msg = $message ?? $error;

        return self::responseError($data, $msg, 422);
    }

    public static function responseValidateError(
        array $error = [],
        string $message = "Error encountered",
        int $statusCode = 400,
        array $options = []
    ): JsonResponse {
        return self::respond($error, 'error', $message, $statusCode, $options);
    }

    public static function responsePaginate(
        $dataQuery,
        Request $request,
        $resource = null
    ): JsonResponse {
        $perPage = $request->query('per_page', 10);
        $data = $dataQuery->paginate($perPage);

        if ($data) {
            if ($resource){
                $response_data = $resource::collection($data->items());
            } else {
                $response_data = $data->items();
            }
            $response = [
                'count' => $data->total(),
                'total_pages' => $data->lastPage(),
                'current_page' => $data->currentPage(),
                'data' => $response_data, 
            ];
        } else{
            $response = [
                'count' => 0,
                'total_pages' => 1,
                'current_page' => 1,
                'data' => [], 
            ];
        }
        return response()->json($response);
    }



    /**
     * @param Throwable $exception
     * @param int $statusCode
     * @param string $message
     * @return JsonResponse
     */
    public static function responseException(
        Throwable $exception,
        int $statusCode = 400,
        string $message = "Exception error"
    ): JsonResponse {
        $options = array('trace' => $exception->getTrace());
        if (config('app.env') === 'production') {
            return self::responseError([], $message, $statusCode, []);
        }
        return self::responseError([], $message, $statusCode, []);
        // return self::responseError([], $message, $statusCode, $options);
    }
}
