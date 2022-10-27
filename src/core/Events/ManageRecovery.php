<?php

namespace Hairavel\Core\Events;

/**
 * Manage recovery events
 */
class ManageRecovery
{
    public $id;
    public $class;

    public function __construct($class, $id)
    {
        $this->id = $id;
        $this->class = $class;
    }

}

