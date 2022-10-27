<?php

namespace Hairavel\Core\Events;

/**
 * Manage form events
 */
class ManageForm
{
    public $form;
    public $class;

    public function __construct($class, $form)
    {
        $this->form = $form;
        $this->class = $class;
    }

}

