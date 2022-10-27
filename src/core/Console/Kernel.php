<?php

namespace Hairavel\Core\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Str;

class Kernel extends ConsoleKernel
{

    public function __construct(Application $app, Dispatcher $events)
    {
        parent::__construct($app, $events);
    }

    /**
     * define the command
     * @var array
     */
    protected $commands = [
        \Hairavel\Core\Console\AppBuild::class,
        \Hairavel\Core\Console\App::class,
        \Hairavel\Core\Console\AppAdmin::class,
        \Hairavel\Core\Console\AppModel::class,
        \Hairavel\Core\Console\Install::class,
        \Hairavel\Core\Console\Uninstall::class,
        \Hairavel\Core\Console\Operate::class,
        \Hairavel\Core\Console\Visitor::class,
    ];

    /**
     * Scheduling commands
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // perform daily visitor cleanup
        $schedule->command('visitor:clear')->daily();
        // Clean up the operation log every Wednesday
        $schedule->command('operate:clear')->weeklyOn(3);
    }

    /**
     * register command
     * @return void
     */
    protected function commands()
    {
        $list = \Hairavel\Core\Util\Cache::globList(base_path('modules') . '/*/Console/*.php');
        foreach ($list as $file) {
            $this->commands[] = file_class($file);
        }

        //$this->load(__DIR__ . '/Commands');
    }
}
