<?php

namespace Hairavel\Core\UI\Form;

/**
 * Single box
 * @package Hairavel\Core\UI\Form
 */
class Radio extends Element implements Component
{
    protected array $box = [];
    protected $data = [];
    protected string $switch = '';
    protected array $disabled = [];

    /**
     * Select constructor.
     * @param string $name
     * @param string $field
     * @param null|array|callable $data
     * @param string $has
     */
    public function __construct(string $name, string $field, $data = [], string $has = '')
    {
        $this->name = $name;
        $this->field = $field;
        $this->data = $data;
        $this->has = $has;
    }

    /**
     * add options
     * @param $name
     * @param $value
     * @return $this
     */
    public function add($name, $value): self
    {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * switch components
     * @param $group
     * @return $this
     */
    public function switch($group): self
    {
        $this->switch = $group;
        return $this;
    }

    /**
     * set disable option
     * @param $disabled ['value1', 'value2']
     * @return $this
     */
    public function disabled(array $disabled): self
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * Type selection
     * @param array $data
     * @return $this
     */
    public function box(array $data): self
    {
        $this->box = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function render(): array
    {

        $data = [];
        if ($this->data instanceof \Closure) {
            $data = call_user_func($this->data);
        }
        if (is_array($this->data)) {
            $data = $this->data;
        }
        $this->data = $data;


        $child = [];
        foreach ($data as $key => $vo) {
            $item = [
                'nodeName' => 'a-radio',
                'child' => $vo,
                'value' => $key,
            ];
            if (in_array($key, $this->disabled)) {
                $item['disabled'] = true;
            }
            $child[] = $item;
        }

        $data = [
            'nodeName' => 'a-radio-group',
            'name' => $this->field,
            'vModel:modelValue' => $this->getModelField(),
            'child' => $child
        ];

        if ($this->replace != '') {
            $data['vStringReplace'] = $this->replace;
        }

        return $data;
    }

    public function dataValue($value)
    {
        return $this->getValue($value) ?? array_key_first((array)$this->data);
    }
}
