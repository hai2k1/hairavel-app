<?php

namespace Hairavel\Core\UI\Widget;

/**
 * Class Form
 * @package Hairavel\Core\UI\Widget
 */
class Form extends Widget
{

    private \Hairavel\Core\UI\Form $form;

    public function __construct($data, callable $callback = null)
    {
        $this->callback = $callback;
        $this->form = new \Hairavel\Core\UI\Form($data, false);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return $this->form->render();
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->form->$method(...$arguments);
    }

}
