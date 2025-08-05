<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiAuthException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated .
     * @return string|null
     */
    protected function redirectTo($request): null|string
    {
        if (! $request->expectsJson()) {
            throw new ApiAuthException($request);
        }
        return null;
    }
}
