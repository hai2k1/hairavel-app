<?php

namespace Hairavel\Core\Events;

/**
 * Manage form events
 */
class ManageTable
{
    public $table;
    public $class;

    public function __construct($class, $table)
    {
        $this->table = $table;
        $this->class = $class;
    }

}

