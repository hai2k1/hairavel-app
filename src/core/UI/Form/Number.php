<?php

namespace Hairavel\Core\UI\Form;

/**
 * Numerical input
 * @package Hairavel\Core\UI\Form
 */
class Number extends Element implements Component
{
    protected Text $object;
    /**
     * @var int|float
     */
    protected $step = 1;
    /**
     * @var ?int|float
     */
    protected $max = null;
    /**
     * @var int|float
     */
    protected $min = 0;
    protected ?int $precision = null;

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
     * max
     * @param int|float $default
     * @return $this
     */
    public function max($default = 0): self
    {
        $this->max = $default;
        return $this;
    }

    /**
     * minimum
     * @param int|float $default
     * @return $this
     */
    public function min($default = 0): self
    {
        $this->min = $default;
        return $this;
    }

    /**
     * Step value
     * @param int|float $default
     * @param int|null $precision
     * @return $this
     */
    public function step($default = 1, ?int $precision = null): self
    {
        $this->step = $default;
        $this->precision = $precision;
        return $this;
    }

    /**
     * @return array
     */
    public function render(): array
    {
        $data = [
            'nodeName' => 'a-input-number',
            'placeholder' => $this->attr['placeholder'] ?: 'Please enter' . $this->name,
            'vModel:modelValue' => $this->getModelField(),
            'step' => $this->step,
            'min' => $this->min,
            'mode' => 'button'
        ];
        if ($this->max) {
            $data['max'] = $this->max;
        }
        if ($this->precision) {
            $data['precision'] = $this->precision;
        }
        if($this->replace != ''){
            $data['vStringReplace'] = $this->replace;
        }
        return $data;
    }

}
