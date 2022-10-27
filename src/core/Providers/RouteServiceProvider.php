<?php

namespace Hairavel\Core\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    public function boot()
    {

        // register public routes
        $this->app['router']->group(['prefix' => 'service', 'middleware' => ['web']], function () {
            $list = \Hairavel\Core\Util\Cache::routeList('Service');
            foreach ($list as $file) {
                if (is_file($file)) {
                    $this->loadRoutesFrom($file);
                }
            }
        });
        $this->app['router']->group(['prefix' => 'api', 'middleware' => ['api'], 'statis' => true], function () {
            $list = \Hairavel\Core\Util\Cache::routeList('Api');
            foreach ($list as $file) {
                if (is_file($file)) {
                    $this->loadRoutesFrom($file);
                }
            }
        });
        $this->app['router']->group(['prefix' => 'api', 'middleware' => ['api', 'auth.api']], function () {
            $list = \Hairavel\Core\Util\Cache::routeList('AuthApi');
            foreach ($list as $file) {
                if (is_file($file)) {
                    $this->loadRoutesFrom($file);
                }
            }
        });
        $this->app['router']->group(['middleware' => ['web'], 'statis' => true], function () {
            $list = \Hairavel\Core\Util\Cache::routeList('Web');
            foreach ($list as $file) {
                if (is_file($file)) {
                    $this->loadRoutesFrom($file);
                }
            }
        });

        // request frequency limit
        /*RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });*/
    }
}
