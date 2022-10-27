<?php

namespace Hairavel\Core\Middleware;

use Hairavel\Core\Facades\Permission;
use Hairavel\Core\Util\View;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class Manage extends BaseMiddleware
{

    public function handle($request, \Closure $next)
    {
        // render front-end entry
        if (!request()->expectsJson()) {
            return View::manage();
        }

        $layer = strtolower(app_parsing('layer'));

        // Check if there is a token in this request
        $this->checkForToken($request);
        try {
            // Check the user's login status
            if (!auth($layer)->getToken()) {
                app_error('Please login first', 401);
            }
            if (!$payload = auth($layer)->payload()) {
                app_error('Please login first', 401);
            }
            $this->checkPurview($layer, $request);
            return $next($request);
        } catch (TokenExpiredException $exception) {
            try {
                // refresh token
                $token = auth($layer)->refresh();
                $request->headers->set('Authorization', 'Bearer ' . $token);
                // use one-time login
                auth($layer)->onceUsingId(
                    $payload['sub']
                );
                $this->checkPurview($layer, $request);
            } catch (JWTException $exception) {
                app_error('Login failed', 401);
            }
        }
        return $this->setAuthenticationHeader($next($request), $token);
    }

    private function checkPurview($layer, $request)
    {
        $user = auth($layer)->user();
        if (!$user) {
            app_error('Login failed', 401);
        }
        $payload = auth($layer)->payload();

        // register permission
        $request->attributes->add([
            'global_guard_id' => $payload['guard_id'] ?: null
        ]);
        \Hairavel\Core\Facades\Permission::register($layer, $payload['guard_id'] ?: null);

        // permission check
        $public = request()->route()->getAction('public');
        if ($public) {
            return true;
        }

        $name = request()->route()->getName();
        if (!$user->can($name)) {
            app_error('No permission to use this function', 403);
        }
    }
}
