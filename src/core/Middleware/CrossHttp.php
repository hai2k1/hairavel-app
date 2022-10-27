<?php

namespace Hairavel\Core\Middleware;

use Closure;

class CrossHttp
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', '*'); //Allow all resources to cross domain
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept, Authorization, application/json , X-Auth-Token');//response headers allowed to pass
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');//allowed request methods
        $response->header('Access-Control-Expose-Headers', 'Authorization');//Allow axios to get the Authorization in the response header
        $response->header('Allow', 'GET, POST, PATCH, PUT, OPTIONS, delete');//allowed request methods
        $response->header('Access-Control-Allow-Credentials', 'true');//Run the client with certificate access
        return $response;
    }
}
