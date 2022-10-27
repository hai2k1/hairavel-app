<?php

namespace Hairavel\Core\UI\Form;

/**
 * Date time picker
 * @package Hairavel\Core\UI\Form
 */
class Datetime extends Element implements Component
{

    protected string $string = 'YYYY-MM-DD HH:mm';

    /**
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
     * date format
     * @param string $format
     * @return $this
     */
    public function string(string $format): self
    {
        $this->string = $format;
        return $this;
    }

    /**
     * render component
     * @return array
     */
    public function render(): array
    {
        $data = [
            'nodeName' => 'a-date-picker',
            'vModel:value' => $this->getModelField(),
            'showTime' => true,
            'allowClear' => true,
            'format' => $this->string,
            'placeholder' => $this->attr['placeholder'] ?: 'Please choose' . $this->name,
            'vModel:modelValue' => $this->getModelField()
        ];

        if($this->replace != ''){
            $data['vStringReplace'] = $this->replace;
        }

        return $data;
    }

    /**
     * @param $data
     * @return string|null
     */
    public function dataInput($data): ?string
    {
        return $data ? date('Y-m-d H:i:s', strtotime($data)) : null;
    }

    /**
     * @param $data
     * @return string|null
     */
    public function dataValue($data): ?string
    {
        $data = $this->getValue($data);
        return $data ?: null;
    }

}
