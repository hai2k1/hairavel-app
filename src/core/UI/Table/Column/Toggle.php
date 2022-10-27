<?php

namespace Hairavel\Core\UI\Table\Column;

/**
 * Table editing - switch (real-time editing)
 */
class Toggle implements Component
{

    private string $route;
    private array $params;
    private string $field;
    private array $fields;

    private string $size = 'medium';
    private string $type = 'circle';
    private $checkedValue = 1;
    private $uncheckedValue = 0;
    private $checkedColor = '';
    private $uncheckedColor = '';
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
     * Set column fields
     *
     * @param array $fields
     * @return $this
     */
    public function fields(array $fields = []): self
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * switch data
     *
     * @param string|number|boolean $checkedValue value when checked
     * @param string|number|boolean $uncheckedValue unchecked value
     * @return $this
     */
    public function data($checkedValue, $uncheckedValue)
    {
        $this->checkedValue = $checkedValue;
        $this->uncheckedValue = $uncheckedValue;
        return $this;
    }

    /**
     * switch color
     *
     * @param string $checkedColor switch color when checked
     * @param string $uncheckedColor switch color when unchecked
     * @return $this
     */
    public function color($checkedColor, $uncheckedColor = '')
    {
        $this->checkedColor = $checkedColor;
        $this->uncheckedColor = $uncheckedColor;
        return $this;
    }

    /**
     * Type of switch
     *
     * @param string $type 'circle' | 'round' | 'line'
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * The size of the switch
     *
     * @param string $size 'small' | 'medium'
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
     * @param $label
     * @return string[]
     */
    public function render($label): array
    {
        $url = app_route($this->route, $this->params, false, 'rowData.record', $this->fields);

        $node = [
            'nodeName' => 'a-switch',
            'vModel:model-value' => "rowData.record['$label']",
            'vOn:change' => "rowData.record['$label'] = \$event, editValue($url, {'field': '$this->field', '$this->field': rowData. record['$label']})",
            'checkedValue' => $this->checkedValue,
            'uncheckedValue' => $this->uncheckedValue,
        ];

        $this->checkedColor && $node['checkedColor'] = $this->checkedColor;
        $this->uncheckedColor && $node['uncheckedColor'] = $this->uncheckedColor;
        $this->size && $node['size'] = $this->size;
        $this->type && $node['type'] = $this->type;

        return array_merge($node, (array)$this->attr);
    }

}
