<?php

namespace Hairavel\Core\UI\Widget;

/**
 *Class Images
 * @package Hairavel\Core\UI\Widget
 */
class Images extends Widget
{

    private array $list;
    private int $size = 60;

    /**
     *Image constructor.
     * @param array $list
     * @param callable|null $callback
     */
    public function __construct(array $list, callable $callback = NULL)
    {
        $this->list = $list;
        $this->callback = $callback;
    }

    /**
     * image size
     * @param int $size
     * @return $this
     */
    public function size(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return array
     */
    public function render(): array
    {
        $list = [];
        foreach ($this->list as $vo) {
            $list[] = [
                'nodeName' => 'a-image',
                'src' => $vo,
                'width' => $this->size,
                'height' => $this->size,
            ];
        }

        return [
            'nodeName' => 'a-image-preview-group',
            'infinite' => true,
            'child' => [
                'nodeName' => 'a-space',
                'child' => $list
            ]
        ];

    }

}
