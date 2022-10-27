<?php

namespace Hairavel\Core\UI\Form;

/**
 * Class Time
 * time picker
 * @package Hairavel\Core\UI\Form
 */
class Time extends Element implements Component
{
    protected string $string = 'HH:mm:ss';

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
     * Time format
     * @param string $format
     * @return $this
     */
    public function string(string $format): self
    {
        $this->string = $format;
        return $this;
    }

    /**
     * @return array
     */
    public function render(): array
    {
        return [
            'nodeName' => 'a-time-picker',
            'allowClear' => true,
            'format' => $this->string,
            'placeholder' => $this->attr['placeholder'] ?: 'Please choose' . $this->name,
            'vModel:model-value' => $this->getModelField()
        ];
    }

}
