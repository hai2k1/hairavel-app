<?php

namespace Hairavel\Core\Events;

/**
 * Manage empty events
 */
class ManageClear
{
    public $id;
    public $class;

    public function __construct($class, $id)
    {
        $this->id = $id;
        $this->class = $class;
    }

}

