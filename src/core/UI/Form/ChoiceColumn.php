<?php

namespace Hairavel\Core\UI\Form;

/**
 * Table association selector column
 * @package Hairavel\Core\UI\Form
 */
class ChoiceColumn
{
    /**
     * @var array
     */
    private array $column;

    /**
     * @param string $name
     * @param string $field
     * @return $this
     */
    public function text(string $name, string $field): self
    {
        $this->column[] = [
            'name' => $name,
            'key' => $field,
            'type' => 'text'
        ];
        return $this;
    }

    /**
     * @param string $name
     * @param string $field
     * @return $this
     */
    public function image(string $name, string $field): self
    {
        $this->column[] = [
            'name' => $name,
            'key' => $field,
            'type' => 'image'
        ];
        return $this;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->column;
    }

}
