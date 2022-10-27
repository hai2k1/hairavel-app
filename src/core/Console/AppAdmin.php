<?php

namespace Hairavel\Core\Console;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AppAdmin extends \Hairavel\Core\Console\Common\Stub
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-admin {name} {--class= : class name} {--title= : function name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin controller';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = lcfirst($this->argument('name'));
        $title = $this->option('title');
        $fun = lcfirst($this->option('class'));
        $app = ucfirst($name);
        if (!is_dir(base_path('/modules/' . $app))) {
            $this->error('The application does not exist, please check!');
            exit;
        }
        if (!$fun) {
            $fun = lcfirst($this->getAppName('Please enter the class name'));
        }
        $class = ucfirst($fun);
        if (!$title) {
            $title = $this->ask('Please enter the function name');
        }
        $modelClass = '\Hairavel\Core\Model\Base';

        // create controller
        $this->generatorFile($app . "/Admin/{$class}.php", __DIR__ . '/Tpl/AppAdmin/Admin.stub', [
            'app' => $app,
            'title' => $title,
            'class' => $class,
            'modelClass' => $modelClass,
        ]);

        // create route
        $this->appendFile($app . '/Route/AuthAdmin.php', <<<EOL
                                    Route::group([
                                        'auth_group' => '$title'
                                    ], function () {
                                        Route::manage(\\Modules\\$app\Admin\\$class::class)->make();
                                    });
                                    EOL,
            '// Generate Route Make');

        // create menu
        $this->appendFile($app . '/Menu/Admin.php',
            <<<EOL
        Menu::link('$title', 'admin.$name.$fun');
EOL,
            '// Generate Menu Make');

        $this->info('Model created successfully');
    }

    public function appendFile($file, $content = '', $mark = '')
    {
        $file = base_path('/modules/' . $file);
        $data = [];
        $contentData = explode("\n", $content);
        foreach (file($file) as $line) {
            if (strpos($line, $mark) !== false) {
                $place = substr($line, 0, strrpos($line, $mark));
                foreach ($contentData as $content) {
                    $data[] = $place . $content . "\n";
                }
            }
            $data[] = $line;
        }
        file_put_contents($file, implode("", $data));
    }

}
