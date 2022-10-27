<?php

namespace Hairavel\Core\Console;

class App extends \Hairavel\Core\Console\Common\Stub
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make
                                {name : application name}
                                {--title= : application name}
                                {--desc= : application description}
                                {--auth= : app author}
                                ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create application structure';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $title = $this->option('title') ?: 'Application name';
        $desc = $this->option('desc') ?: 'Application description';
        $auth = $this->option('auth') ?: 'App Author';
        $name = $this->argument('name');
        $appDir = ucfirst($name);
        if (is_dir(base_path('/modules/' . $appDir))) {
            $this->error('The application already exists, please change the application name!');
            exit;
        }
        // create application structure
        $this->generatorDir($appDir);
        $this->generatorDir($appDir . '/' . 'Admin');
        $this->generatorDir($appDir . '/' . 'Api');
        $this->generatorDir($appDir . '/' . 'Model');
        $this->generatorDir($appDir . '/' . 'Config');
        $this->generatorDir($appDir . '/' . 'Menu');
        $this->generatorDir($appDir . '/' . 'Route');
        $this->generatorDir($appDir . '/' . 'Service');
        $this->generatorDir($appDir . '/' . 'View');
        $this->generatorDir($appDir . '/' . 'View/Admin');
        // create initial file
        $this->generatorFile($appDir . '/' . 'Config/Config.php', __DIR__ . '/Tpl/App/Config.stub', [
            'title' => $title,
            'system' => 0,
            'auth' => $auth,
            'desc' => $desc,
        ]);
        $this->generatorFile($appDir . '/' . 'Menu/Admin.php', __DIR__ . '/Tpl/App/Menu.stub', [
            'appDir' => $appDir,
            'name' => $name,
            'menu' => $title,
            'icon' => '',
        ]);
        $this->generatorFile($appDir . '/' . 'Route/AuthAdmin.php', __DIR__ . '/Tpl/App/AuthAdmin.stub', [
            'title' => $title,
            'name' => $name,
        ]);
        $this->callSilent('app:build');
        $this->info('The application was created successfully');
    }

}
