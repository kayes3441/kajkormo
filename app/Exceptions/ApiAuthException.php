<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
class ApiAuthException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json([
            'message' => 'Access denied.',
            'error_code' => 'auth-error',
        ], 401);
    }
}
