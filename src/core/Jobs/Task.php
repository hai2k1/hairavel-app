<?php

namespace Hairavel\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Task implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Task class
     * @var string
     */
    protected $class;

    /**
     * Task method
     * @var string
     */
    protected $method;

    /**
     * Task parameters
     * @var array
     */
    protected $params = [];

    /**
     * number of retries
     * @var int
     */
    public $tries = 10;

    /**
     * Task constructor.
     * @param string $class
     * @param string $method
     * @param array $params
     * @param $delay
     */
    public function __construct(string $class, string $method, array $params = [], $delay = 3)
    {
        $this->class = $class;
        $this->method = $method;
        $this->params = $params;
        $this->delay($delay);
    }

    /**
     * perform tasks
     */
    public function handle()
    {
        try {
            app($this->class)->{$this->method}(...$this->params);
        } catch (\Exception $exception) {
            Log::critical('[Task] Task execution exception', [
                'error' => $exception->getMessage(),
                'method' => $this->class . '@' . $this->method,
                'params' => $this->params
            ]);
            // number of retries * 10 seconds
            $this->release($this->attempts() * 5);
        }
    }
}
