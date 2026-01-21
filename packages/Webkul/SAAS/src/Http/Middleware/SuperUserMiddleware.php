<?php

namespace Webkul\SAAS\Http\Middleware;

use Closure;

class SuperUserMiddleware
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
        if (!auth()->guard('user')->check() || !auth()->guard('user')->user()->is_superuser) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
