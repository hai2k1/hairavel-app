<?php

namespace Hairavel\Core\Providers;

use Hairavel\Core\Events\ServiceBoot;
use Hairavel\Core\Events\ServiceRegister;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        // call system extension
        event(new ServiceRegister);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        // register database directory
        $this->loadMigrationsFrom(realpath(__DIR__ . '/../../../database/migrations'));

        // call system extension
        event(new ServiceBoot);
    }
}
