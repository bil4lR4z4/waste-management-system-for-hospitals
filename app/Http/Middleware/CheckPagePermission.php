<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPagePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $page, $action = 'view'): Response
    {
        $user = auth()->user();
        if(!$user){
            abort(403, 'Unauthorized');
        }

        if ($user->trash == 1) {
            auth()->logout();
            abort(403, 'Your account is blocked.');
        }

        if(!$user->hasPermission($page, $action)){
            abort(403, 'You do not have permission to access this page.');
        }
        
        return $next($request);
    }
}
