<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if(Auth::guard('api')->user() === null) {
        //     return response("Unauthorized Error", 401);
        // }
        $action = $request->route()->getAction();
        $roles = isset($action['roles']) ? $action['roles'] : null;
        if(auth('api')->user()->hasAnyRole($roles)) {
            return $next($request);  
        }
        return response("Unauthorized Error", 401); 
    }
}
