<?php

namespace Ashamnx\Acl\Middleware;

use Ashamnx\Acl\Acl;
use Closure;
use Illuminate\Support\Facades\Auth;

class AclMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()){
            $user = Auth::user();
            if (!Acl::check($user->id, $request->route())) {
                return response([
                    'success' => false,
                    'message' => 'User not authorised to perform action'
                ], 401);
            }
            return $next($request);
        };
        return response([
            'success' => false,
            'message' => 'User not logged in'
        ], 401);

    }

}