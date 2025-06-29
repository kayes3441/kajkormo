<?php

namespace App\Http\Middleware;


use App\Trait\AdminPermissionTrait;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermission
{
    use AdminPermissionTrait;
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->getAccessPermission($request) || str_ends_with($request->path(), 'login')) {
            return $next($request);
        }
        Notification::make()
            ->title('Access denied!!!!')
            ->info()
            ->send();
        return redirect()->back();
    }
}
