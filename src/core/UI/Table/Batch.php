<?php

namespace Hairavel\Core\UI\Table;

use Hairavel\Core\UI\Widget\Link;
use Hairavel\Core\UI\Form\Select;

/**
 * Operation batch
 * Class Column
 * @package Hairavel\Core\UI\Filter
 */
class Batch
{

    protected array $nodes = [];
    protected array $select = [];
    protected array $url = [];

    /**
     * button
     * @param string $name
     * @param string $type
     * @param string $route
     * @param array $params
     * @param string $btnType
     * @return $this
     */
    public function button(string $name, string $type = '', string $route = '', array $params = [], string $btnType = 'normal'): self
    {
        $params['bath_type'] = $type;
        $url = route($route, $params);
        $this->nodes[] = [
            'nodeName' => 'a-button',
            'type' => 'secondary',
            'status' => $btnType,
            'child' => $name,
            'vOn:click' => "footer.checkAction('$url', 'Are you sure to execute the $name\ action?')"
        ];
        return $this;
    }

    /**
     * @param string $name
     * @param string $route
     * @param array $params
     * @return $this
     */
    public function select(string $name, string $route = '', array $params = []): self
    {
        $url = route($route, $params);
        $this->select[] = [
            'nodeName' => 'a-doption',
            'child' => $name,
            'vOn:click' => "footer.checkAction('$url' 'Are you sure to execute the $name\ action?')"
        ];
        return $this;
    }

    /**
     * render component
     * @return array
     */
    public function render(): array
    {
        if ($this->select) {
            $this->nodes[] = [
                'nodeName' => 'a-dropdown',
                'child' => [
                    [
                        'nodeName' => 'a-button',
                        'type' => 'secondary',
                        'child' => 'batch operation',
                    ],
                    [
                        'vSlot:content' => '',
                        'nodeName' => 'div',
                        'child' => $this->select
                    ]
                ],
            ];
        }

        return $this->nodes;
    }

}
