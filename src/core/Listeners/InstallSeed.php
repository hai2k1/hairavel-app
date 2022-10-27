<?php

namespace Hairavel\Core\Listeners;

/**
 * Data installation interface
 */
class InstallSeed
{

    /**
     * @param $event
     * @return array[]
     */
    public function handle($event)
    {
        return \Hairavel\Database\Seeders\DatabaseSeeder::class;
    }
}
