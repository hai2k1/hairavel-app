<?php

namespace Hairavel\Core\UI\Form;

/**
 * Class Select
 * @package Hairavel\Core\UI\Form
 */
class Select extends Element implements Component
{
    protected string $url = '';
    protected string $route = '';
    protected int $tagCount = 0;
    protected array $params = [];
    protected bool $tip = false;
    protected bool $search = false;
    protected bool $multi = false;
    protected array $optionRender = [];
    protected bool $vChild = false;
    protected $data;

    /**
     * Select constructor.
     * @param string $name
     * @param string $field
     * @param null $data
     * @param string $has
     */
    public function __construct(string $name, string $field, $data = null, string $has = '')
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
        $this->data[$value] = $name;
        return $this;
    }

    /**
     * Additional parameters
     * @param $key
     * @param $value
     * @return $this
     */
    public function nParams($key,$value){
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Default prompt
     * @param bool $tip
     * @return $this
     */
    public function tip(bool $tip = true): self
    {
        $this->tip = $tip;
        return $this;
    }

    /**
     * search
     * @param bool $search
     * @return $this
     */
    public function search(bool $search = true): self
    {
        $this->search = $search;
        return $this;
    }

    /**
     * search address
     * @param string $url
     * @return $this
     */
    public function url(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * routing address
     * @param string $route
     * @param array $params
     * @return $this
     */
    public function route(string $route, array $params = []): self
    {
        $this->route = $route;
        $this->url = app_route($route, $params);
        return $this;
    }

    /**
     * Multiple choice
     * @param int $count
     * @return $this
     */
    public function multi(int $count = 0): self
    {
        $this->multi = true;
        $this->tagCount = $count;
        return $this;
    }

    /**
     * Get the upper-level parameter ID
     * @param bool $vChild
     * @return $this
     */
    public function vChild(bool $vChild = true): self
    {
        $this->vChild = $vChild;
        return $this;
    }

    /**
     * option rendering
     * @param array $data JS Node
     * @return $this
     */
    public function optionRender(array $data): self
    {
        $this->optionRender = $data;
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

        $options = [];
        foreach ($data as $key => $vo) {
            $options[] = [
                'label' => $vo,
                'value' => $key
            ];
        }

        $nParamsName = $this->vChild ? 'vChild:nParams' : 'nParams';
        $params = array_merge([
            'placeholder' => $this->attr['placeholder'] ?: 'Please choose' . $this->name
        ],$this->params);

        if(!empty($options)){
            $params['options'] = $options;
        }

        $object = [
            'nodeName' => 'app-select'
        ];

        if ($this->model) {
            $object['vModel:value'] = $this->getModelField();
        }
        $params['allowClear'] = true;
        if ($this->multi) {
            $params['multiple'] = true;
        }
        if ($this->url) {
            $params['allowSearch'] = true;
            $params['filterOption'] = false;
            if ($this->route) {
                $object['vBind:dataUrl'] = $this->url;
            } else {
                $object['dataUrl'] = $this->url;
            }
        }
        if ($this->search) {
            $params['allowSearch'] = true;
        }
        if ($this->tagCount) {
            $params['maxTagCount'] = $this->tagCount;
        }
        if ($this->optionRender) {
            $object['vRender:optionRender:item'] = $this->optionRender;
        }

        $object[$nParamsName] = $params;

        return $object;
    }

    /**
     * @param $value
     * @return array|mixed
     */
    public function dataValue($value)
    {
        return $this->multi ? array_values(array_filter((array)$this->getValueArray($value))) : $this->getValue($value);
    }

    /**
     * @param $data
     * @return string
     */
    public function dataInput($data): ?string
    {
        return is_array($data) ? implode(',', $data) : $data;
    }

}
