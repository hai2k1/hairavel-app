<?php

namespace Hairavel\Core\UI\Form;

/**
 *Class Textarea
 * Multi-line input box
 * @package Hairavel\Core\UI\Form
 */
class Textarea extends Element implements Component
{
    protected int $limit = 0;

    /**
     * Text constructor.
     * @param string $name
     * @param string $field
     * @param string $has
     */
    public function __construct(string $name, string $field, string $has = '')
    {
        $this->name = $name;
        $this->field = $field;
        $this->has = $has;
    }

    /**
     * @param $num
     * @return $this
     */
    public function limit($num): self
    {
        $this->limit = $num;
        return $this;
    }

    /**
     * @return array
     */
    public function render(): array
    {
        $data = [
            'nodeName' => 'a-textarea',
            'vModel:modelValue' => $this->getModelField(),
            'placeholder' => $this->attr['placeholder'] ?: 'Please enter' . $this->name,
            'allowClear' => true,
            'showWordLimit' => true
        ];
        if ($this->limit) {
            $data['maxLength'] = $this->limit;
        }

        if($this->replace != ''){
            $data['vStringReplace'] = $this->replace;
        }

        return $data;
    }

}
