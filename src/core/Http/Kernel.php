<?php

namespace Hairavel\Core\Http;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;

class Kernel extends \Illuminate\Foundation\Http\Kernel
{

    /**
     * Global middle layer
     * @var array
     */
    protected $middleware = [
        \Hairavel\Core\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,

        \Hairavel\Core\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,

        \Hairavel\Core\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,

        \Hairavel\Core\Middleware\CheckInstall::class,
        \Hairavel\Core\Middleware\VisitorBefore::class,
        \Hairavel\Core\Middleware\VisitorAfter::class
    ];
    /**
     * Routing packet middle layer
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Hairavel\Core\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,

            // \Hairavel\Core\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'api' => [
            \Hairavel\Core\Middleware\Header::class,
            // 'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Hairavel\Core\Middleware\Api::class,
        ],

        'auth.manage' => [
            'web',
            \Hairavel\Core\Middleware\Manage::class,
        ],

        'auth.manage.register' => [
            'web',
            \Hairavel\Core\Middleware\ManageRegister::class,
        ]
    ];

    /**
     * Routing independent middle layer
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'=>Authenticate::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
