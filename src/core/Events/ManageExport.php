<?php

namespace Hairavel\Core\Events;

/**
 * Manage export events
 */
class ManageExport
{
    public $table;
    public $class;

    public function __construct($class, $table)
    {
        $this->table = $table;
        $this->class = $class;
    }

}

