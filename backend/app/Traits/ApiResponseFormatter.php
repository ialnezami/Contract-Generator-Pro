<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponseFormatter
{
    /**
     * Success response.
     */
    protected function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString(),
        ], $statusCode);
    }

    /**
     * Error response.
     */
    protected function errorResponse(string $message = 'Error', int $statusCode = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Resource response.
     */
    protected function resourceResponse(JsonResource $resource, string $message = 'Resource retrieved successfully'): JsonResponse
    {
        return $this->successResponse($resource, $message);
    }

    /**
     * Collection response.
     */
    protected function collectionResponse(ResourceCollection $collection, string $message = 'Resources retrieved successfully'): JsonResponse
    {
        return $this->successResponse($collection, $message);
    }

    /**
     * Created response.
     */
    protected function createdResponse($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Updated response.
     */
    protected function updatedResponse($data = null, string $message = 'Resource updated successfully'): JsonResponse
    {
        return $this->successResponse($data, $message);
    }

    /**
     * Deleted response.
     */
    protected function deletedResponse(string $message = 'Resource deleted successfully'): JsonResponse
    {
        return $this->successResponse(null, $message);
    }

    /**
     * Paginated response.
     */
    protected function paginatedResponse($data, string $message = 'Resources retrieved successfully'): JsonResponse
    {
        return $this->successResponse($data, $message);
    }
}
