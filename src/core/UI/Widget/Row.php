<?php

namespace Hairavel\Core\UI\Widget;

use Hairavel\Core\UI\Tools;

/**
 * Row layout component
 *Class Row
 * @package Hairavel\Core\UI\Widget
 */
class Row extends Widget
{

    private array $column = [];

    /**
     * @param callable|null $callback
     */
    public function __construct(callable $callback = NULL)
    {
        $this->callback = $callback;
    }

    /**
     * set column
     * @param callable $callback
     * @param int $width
     * @return $this
     */
    public function column(callable $callback, int $width = 0): self
    {
        $this->column[] = [
            'width' => $width,
            'callback' => $callback,
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function render(): array
    {

        $nodes = [];
        foreach ($this->column as $vo) {
            $nodes[] = [
                'nodeName' => 'div',
                'class' => $vo['width'] ? "row-span-{$vo['width']}" : '',
                'child' => call_user_func($vo['callback'])
            ];
        }
        return [
            'nodeName' => 'div',
            'class' => 'grid grid-flow-col gap-x-4',
            'child' => $nodes
        ];


    }

}
