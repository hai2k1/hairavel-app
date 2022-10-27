<?php

namespace Hairavel\Core\UI\Table;

use Hairavel\Core\UI\Widget\Link;

/**
 * tree list
 * @package Hairavel\Core\UI\Filter
 */
class Tree
{

    protected string $label = '';
    protected array $node = [];
    protected array $prefix = [];
    protected array $suffix = [];
    protected Column\Link $link;

    /**
     * @param string $label
     * @param array $node
     */
    public function __construct(string $label, array $node = [])
    {
        $this->label = $label;
        $this->node = $node;

    }

    /**
     * @param $node
     * @return $this
     */
    public function prefix($node): self
    {
        $this->prefix = $node;
        return $this;
    }

    /**
     * @param $node
     * @return $this
     */
    public function suffix($node): self
    {
        $this->suffix = $node;
        return $this;
    }

    /**
     * Add a link
     * @param string $name
     * @param string $route
     * @param array $params
     * @return Link
     */
    public function link(string $name, string $route, array $params = []): Link
    {
        if (!$this->link) {
            $this->link = new Column\Link();
        }
        return $this->link->add($name, $route, $params);
    }


    /**
     * render component
     * @return array
     */
    public function render(): array
    {
        $suffix = $this->suffix;
        if ($this->link) {
            $suffix = $this->link->render('');
        }
        return [
            'node' => $this->node ?: ['nodeName' => 'div', 'child' => "{{rowData.record['$this->label']}}"],
            'prefix' => $this->prefix,
            'suffix' => $suffix
        ];
    }

    /**
     * Component row data
     * @param $rowData
     * @return array
     */
    public function getData($rowData): array
    {
        $data = [];
        // element data
        if ($this->link) {
            $data = array_merge($data, $this->link->getData($rowData));
        }
        return $data;
    }

}
