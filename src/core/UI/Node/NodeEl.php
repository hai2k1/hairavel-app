<?php

namespace Hairavel\Core\UI\Node;


use Hairavel\Core\UI\Node;

/**
 * node attribute
 */
class NodeEl
{

    public string $name = 'div';
    public $callback;
    public array $attr = [];

    /**
     * @param $name
     * @param null $callback
     */
    public function __construct($name, $callback = null)
    {
        $this->name = $name;
        if ($callback instanceof \Closure) {
            $node = new Node();
            $callback($node);
            $this->callback = $node;
        }else {
            $this->callback = $callback;
        }
    }

    /**
     * @return array
     */
    public function render(): array
    {
        $data = [
            'nodeName' => $this->name,
        ];
        if ($this->callback instanceof Node) {
            $data['child'] = $this->callback->render();
        } else if ($this->callback) {
            $data['child'] = $this->callback;
        }
        return array_merge($data, $this->attr);
    }


    public function __call($method, $arguments)
    {
        $this->attr[$method] = $arguments[0];
        return $this;
    }
}
