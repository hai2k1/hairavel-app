<?php

namespace Hairavel\Core\Events;

/**
 * Manage status events
 */
class ManageStatus
{
    public $id;
    public $class;

    public function __construct($class, $id)
    {
        $this->id = $id;
        $this->class = $class;
    }

}

