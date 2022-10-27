<?php

namespace Hairavel\Core\UI\Widget;

use Hairavel\Core\UI\Tools;

/**
 * Class Widget
 * @package Hairavel\Core\UI\Widget
 */
class Widget
{
    protected ?\Closure $callback;

    protected array $class = [];
    protected array $attr = [];
    protected array $style = [];
    protected $callbackData;

    /**
     * attribute data
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function attr(string $name, string $value): self
    {
        $this->attr[$name] = $value;
        return $this;
    }

    /**
     * class style
     * @param string $name
     * @return $this
     */
    public function class(string $name): self
    {
        $this->class[] = $name;
        return $this;
    }

    /**
     * set style
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function style(string $name, string $value): self
    {
        $this->style[$name] = $value;
        return $this;
    }

    /**
     * set variable
     * @param $name
     * @param $value
     * @return $this
     */
    public function setValue($name, $value): self
    {
        $this->$name = $value;
        return $this;
    }

    /**
     * get variable
     * @param $name
     * @return mixed
     */
    public function getValue($name)
    {
        return $this->$name;
    }

    /**
     * merge array
     * @param array $array
     * @param string $str
     * @return string
     */
    public function mergeArray(array $array, string $str = ''): string
    {
        return implode($str, $array);
    }

    /**
     * Callback settings
     * @return $this
     */
    public function next(): Widget
    {
        if (!$this->callback) {
            return $this;
        }
        $this->callbackData = call_user_func($this->callback, $this);
        return $this;
    }

    /**
     * @return array
     */
    public function getRender(): array
    {
        return array_merge($this->render(), $this->attr);
    }


}
