<?php

namespace Hairavel\Core\UI\Widget;

use Hairavel\Core\UI\Tools;

/**
 * Project callback
 * @package Hairavel\Core\UI\Widget
 */
class Item
{
    public $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function __call($method, $arguments)
    {
        $this->{$method}[] = $arguments;
        return $this;
    }

}
