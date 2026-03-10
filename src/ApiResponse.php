<?php

namespace MMT\ApiResponseNormalizer;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    public function success(
        array $data = [],
        string $message = 'Success',
        array $meta = [],
        int $code = 200,
        ?LengthAwarePaginator $paginator = null,
        array $filters = []
    ): JsonResponse
    {
        if ($message !== '') {
            $meta = array_merge($meta, ['message' => $message]);
        }

        if ($paginator !== null) {
            $meta['pagination'] = [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ];
            
            if ($filters !== []) {
                $meta['filters'] = $filters;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => $meta,
        ], $code);
    }

    public function created($data, string $message = 'Created') : JsonResponse
    {
        return $this->success($data, $message, [], 201);
    }

    public function noContent() : Response
    {
        return response()->noContent();
    }

    public function unauthorized(string $message = 'Unauthorized') : JsonResponse
    {
        return $this->error($message, 'UNAUTHORIZED', [], 401);
    }

    public function validationError(array $errors, string $message = 'Validation Error') : JsonResponse
    {
        return $this->error($message, 'VALIDATION_ERROR', $errors, 422);
    }

    public function error(string $message = 'Error', string $errorCode = 'UNKNOWN_ERROR', array $details = [], int $status = 400) : JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'error' => [
                'code' => $errorCode,
                'details' => $details,
            ],
        ], $status);
    }

    public function accepted(array $data = [], string $message = 'Accepted') : JsonResponse
    {
        return $this->success($data, $message, [], 202);
    }
}