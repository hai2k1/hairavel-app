<?php

namespace Hairavel\Core\Console;

use Illuminate\Console\Command;

class Uninstall extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:uninstall {name}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Extended app uninstall';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        if (strpos($name, '/') === false) {
            // local application
            $appDir = ucfirst($name);
            $dir = base_path('modules/' . $appDir);
            $database = $dir . '/Database';
            $migrations = $database . '/Migrations';
            $publish = 'duxapp-' . strtolower($name);
        } else {
            // package application
            $dir = base_path('vendor/' . trim($name, '/'));
            $database = $dir . '/database';
            $migrations = $database . '/migrations';
            $dir = explode('/', $name);
            $publish = str_replace('/', '-', end($dir));
        }

        // data table unload
        if (is_dir($migrations)) {
            $path = $migrations .'/*.php';
            $fileList = glob($path);
            foreach ($fileList as $file) {
                $file = str_replace(base_path(), '', $file);
                $this->callSilent('migrate:reset', [
                    '--path' => $file,
                    '--force' => true,
                ]);
            }
        }

        $this->callSilent('app:build');
        $this->info('Application uninstalled successfully');
    }
}
