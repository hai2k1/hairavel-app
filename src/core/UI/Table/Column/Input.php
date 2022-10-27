<?php

namespace Hairavel\Core\UI\Table\Column;

/**
 * Form editing - input box (real-time editing)
 */
class Input implements Component
{

    private string $route;
    private array $params;
    private string $field;
    private array $fields;
    private string $size = 'small';
    private bool $allowClear = false;
    private array $attr = [];

    /**
     * @param string $field
     * @param string $route
     * @param array $params
     */
    public function __construct(string $field, string $route, array $params = [])
    {
        $this->field = $field;
        $this->params = $params;
        $this->route = $route;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function fields(array $fields = [])
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * input box size
     *
     * @param string $size 'mini' | 'small' | 'medium' | 'large'
     * @return $this
     */
    public function size($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Set additional properties
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function attr(string $name, $value)
    {
        $this->attr[$name] = $value;
        return $this;
    }

    /**
     * Whether to allow clearing the input box
     *
     * @param bool $allowClear
     * @return $this
     */
    public function allowClear($allowClear)
    {
        $this->allowClear = (bool)$allowClear;
        return $this;
    }

    /**
     * @param $label
     * @return array
     */
    public function render($label): array
    {
        $url = app_route($this->route, $this->params, false, 'rowData.record', $this->fields);
        $node = [
            'nodeName' => 'a-input',
            'size' => $this->size,
            'allowClear' => $this->allowClear,
            'style' => 'padding: 0px 6px',
            'vModel:modelValue' => "rowData.record.$label",
            'vOn:blur' => "rowData.record.$label = \$event.target._value, editValue($url, {'field': '$this->field', '$this->field': rowData .record.$label})",
        ];

        return array_merge($node, (array)$this->attr);
    }

}
