<?php

namespace Hairavel\Core\Console;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AppModel extends \Hairavel\Core\Console\Common\Stub
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-model {name} {--table= : table name} {--key= : primary key name} {--del= : delete time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create data model';

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
        $name = $this->argument('name');
        $app = ucfirst($name);
        $table = strtolower($this->option('table'));
        $key = strtolower($this->option('key'));
        $del = strtolower($this->option('del'));
        if (!is_dir(base_path('/modules/' . $app))) {
            $this->error('The application does not exist, please check!');
            exit;
        }
        if (!$table) {
            $table = $this->ask('Please enter the table name (English + underscore)');
        }
        if (!$key) {
            $key = $this->ask('Please enter the primary key');
        }
        $tmpArr = explode('_', $table);
        $modelName = implode('', array_map(function ($vo) {
            return ucfirst($vo);
        }, $tmpArr));

        //create model
        Schema::create($table, function (Blueprint $table) use ($key, $del) {
            $table->increments($key);
            $table->timestamps();
            if ($del) {
                $table->timestamp('deleted_at');
            }
        });
        $this->generatorFile($app . "/Model/{$modelName}.php", __DIR__ . '/Tpl/AppModel/Model.stub', [
            'app' => $app,
            'table' => $table,
            'modelName' => $modelName,
            'key' => $key,
        ]);

        $this->info('Model created successfully');
    }
}
