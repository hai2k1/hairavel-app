<?php

namespace Hairavel\Core\UI\Widget;

/**
 * Class Lists
 *
 * @see [arco.design] https://arco.design/vue/component/list
 * @package Hairavel\Core\UI\Widget
 */
class Lists extends Widget
{

    private array $data = [];

    private string $size = 'medium'; // size: small,medium,large

    /**
     * Lists constructor.
     *
     * @param array $data
     * @param callable|null $callback
     */
    public function __construct($data = [], callable $callback = null)
    {
        $this->data = $data;
        $this->callback = $callback;
    }

    /**
     * Size
     *
     * @param string $size
     * @return $this
     */
    public function size(string $size = ''): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Add common items
     *
     * @param scalar|array $item
     * @return $this
     */
    public function addItem($item)
    {
        $this->data[] = $item;
        return $this;
    }

    /**
     * @return array
     */
    public function render(): array
    {

        $inner = [];
        $i = 0;
        foreach ($this->data as $item) {
            $inner[] = [
                'nodeName' => 'a-list-item',
                'child' => $item,
            ];
        }

        return $inner ? [
            'nodeName' => 'a-list',
            'size' => $this->size,
            'child' => $inner,
        ] : [
            'nodeName' => 'a-empty',
        ];
    }

}
