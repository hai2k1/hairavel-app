<?php

namespace Hairavel\Core\UI;

use Hairavel\Core\UI\Node\NodeEl;

/**
 * node generation tool
 */
class Node
{

    public array $nodes = [];

    /**
     * @return array
     */
    public function render(): array
    {
        $data = [];
        foreach ($this->nodes as $vo) {
            $data[] = $vo->render();
        }
        return $data;
    }

    /**
     * @param $method
     * @param $arguments
     * @return NodeEl
     */
    public function __call($method, $arguments)
    {
        $nodeEl = new NodeEl($method, $arguments[0]);
        $this->nodes[] = $nodeEl;
        return $nodeEl;
    }
}
