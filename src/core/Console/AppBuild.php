<?php

namespace Hairavel\Core\Console;

class AppBuild extends \Hairavel\Core\Console\Common\Stub
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile application structure';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        app(\Hairavel\Core\Util\Build::class)->build();
        $this->info('Compilation structure succeeded');
    }

}
