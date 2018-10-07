<?php

namespace App\Http\Middleware;

use Closure;

class AdminAuth
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
        $AuthUserKey = 'AdminAuthUser';
        if (! $request->session()->has($AuthUserKey))
            return redirect('admin');


        return $next($request);
    }
}
