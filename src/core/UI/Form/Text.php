<?php

namespace Hairavel\Core\UI\Form;

use Hairavel\Core\UI\Widget\Icon;

/**
 * Input box
 * @package Hairavel\Core\UI\Form
 */
class Text extends Element implements Component
{
    protected string $type = 'text';
    protected array $before = [];
    protected array $after = [];

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
     * text type
     * @param $name
     * @return $this
     */
    public function type($name): self
    {
        $this->type = $name;
        return $this;
    }

    /**
     * Front icon
     * @param $content
     * @return $this
     */
    public function beforeIcon($content): self
    {
        $this->before = (new Icon($content))->attr('vSlot:prepend', '')->getRender();
        return $this;
    }

    /**
     * rear icon
     * @param $content
     * @return $this
     */
    public function afterIcon($content): self
    {
        $this->after = (new Icon($content))->attr('vSlot:append', '')->getRender();
        return $this;
    }

    /**
     * prepended text
     * @param $content
     * @return $this
     */
    public function beforeText($content): self
    {
        $this->before = [
            'vSlot:prepend' => '',
            'nodeName' => 'span',
            'child' => $content
        ];
        return $this;
    }

    /**
     * post text
     * @param $content
     * @return $this
     */
    public function afterText($content): self
    {
        $this->after = [
            'vSlot:append' => '',
            'nodeName' => 'span',
            'child' => $content
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function render(): array
    {

        $child = [];
        if ($this->before || $this->after) {
            $child = [
                $this->before,
                $this->after
            ];
        }

        $data = [
            'nodeName' => 'a-input',
            'vModel:modelValue' => $this->getModelField(),
            'child' => $child,
            'placeholder' => 'Please enter' . $this->name
        ];

        if($this->replace != ''){
            $data['vStringReplace'] = $this->replace;
        }

        return $data;
    }


}
