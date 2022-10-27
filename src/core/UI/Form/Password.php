<?php

namespace Hairavel\Core\UI\Form;

/**
 * Password input
 * @package Hairavel\Core\UI\Form
 */
class Password extends Element implements Component
{
    protected Text $object;

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
     * @return array
     */
    public function render(): array
    {
        $data = [
            'nodeName' => 'a-input-password',
            'vModel:modelValue' => $this->getModelField(),
            'placeholder' => $this->attr['placeholder'] ?: 'Please enter' . $this->name,
            'allowClear' => true
        ];

        if($this->replace != ''){
            $data['vStringReplace'] = $this->replace;
        }

        return $data;
    }


}
