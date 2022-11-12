<?php

namespace Hairavel\Core\Console;

use Illuminate\Console\Command;

class Visitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitor:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regular cleaning of visitors';

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
        app(\Hairavel\Core\Model\VisitorLog::class)->where('updated_at', '<=', date('Y-m-d H:i:s'))->delete();
    }
}
