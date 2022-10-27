<?php

namespace Hairavel\Core\Events;

/**
 * Manage delete events
 */
class ManageDel
{
    public $id;
    public $class;

    public function __construct($class, $id)
    {
        $this->id = $id;
        $this->class = $class;
    }

}

