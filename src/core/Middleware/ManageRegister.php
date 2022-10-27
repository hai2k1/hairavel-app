<?php

namespace Hairavel\Core\Middleware;

class ManageRegister
{
    public function handle($request, \Closure $next)
    {
        $layer = strtolower(app_parsing('layer'));
        $guard = config('auth.guards.'.$layer . '.provider');
        $model = config('auth.providers.' .$guard . '.model');
        $count = $model::count();
        if ($count) {
            app_error('Login failed, please log in', 401);
        }
        return $next($request);
    }
}
