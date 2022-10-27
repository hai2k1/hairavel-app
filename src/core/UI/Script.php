<?php

namespace Hairavel\Core\UI;

/**
 * Front-end script triggers
 *ClassScript
 * @package Hairavel\Core\UI
 */
class Script
{
    public array $data = [];

    /**
     * Add action
     * @param string $script
     * @return $this
     */
    public function add(string $script): self
    {
        $this->data[] = $script;
        return $this;
    }

    /**
     * render data
     * @param bool $inner
     * @return array|string
     */
    public function render(bool $inner = false)
    {
        $script = implode("\n", $this->data);
        if ($inner) {
            return $script;
        }

        return [
            '__script' => $script
        ];
    }

}
