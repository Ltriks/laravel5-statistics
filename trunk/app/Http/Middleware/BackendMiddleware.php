<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;

class BackendMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 获取
        if (!isAdminLogin()) {
            if (getRequestUri() != '/admin/user/login') {
                return redirect(url('admin'));
            }
        }
        return $next($request);
    }

}
