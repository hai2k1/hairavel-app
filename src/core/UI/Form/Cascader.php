<?php

namespace Hairavel\Core\UI\Form;

/**
 * Cascade selector
 * @package Hairavel\Core\UI\Form
 */
class Cascader extends Element implements Component
{
    protected bool $tip = false;
    protected bool $multi = false;
    protected bool $leaf = true;
    protected array $params = [];
    protected bool $treeData = false;
    protected string $url = '';
    protected string $route = '';
    protected bool $vChild = false;
    protected $data;

    /**
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
     * @param $id
     * @param $pid
     * @return $this
     */
    public function add($name, $id, $pid): self
    {
        $this->data[] = [
            'name' => $name,
            'id' => $id,
            'pid' => $pid
        ];
        return $this;
    }


    /**
     * @param $url
     * @return $this
     */
    public function url($url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
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
     * @return $this
     */
    public function multi(): self
    {
        $this->multi = true;
        return $this;
    }

    /**
     *leaf choice
     * @param bool $leaf
     * @return $this
     */
    public function leaf(bool $leaf): self
    {
        $this->leaf = $leaf;
        return $this;
    }

    /**
     * tree data
     * @param bool $bool
     * @return $this
     */
    public function tree(bool $bool = true): self
    {
        $this->treeData = $bool;
        return $this;
    }

    /**
     * The maximum number of labels displayed
     * @param int $num
     * @return $this
     */
    public function maxTagCount(int $num){
        return $this->nParams('maxTagCount',$num);
    }

    /**
     * Additional parameters
     * @param $key
     * @param $value
     * @return $this
     */
    public function nParams($key,$value): self
    {
        $this->params[$key] = $value;
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
     * @return array
     */
    public function render(): array
    {

        $data = [];
        if ($this->data instanceof \Closure) {
            $data = call_user_func($this->data, $this);
        }
        if (is_array($this->data)) {
            $data = $this->data;
        }

        if (!$this->treeData) {
            $options = [];
            foreach ($data as $vo) {
                $options[] = [
                    'id' => $vo['id'],
                    'pid' => $vo['pid'],
                    'value' => $vo['id'],
                    'label' => $vo['name'],
                ];
            }

            $options = \Hairavel\Core\Util\Tree::arr2tree($options, 'id', 'pid', 'children');

        } else {
            $options = $data;
        }

        $nParamsName = $this->vChild ? 'vChild:nParams' : 'nParams';
        $params = array_merge([
            'check-strictly' => !$this->leaf,
            'multiple' => $this->multi,
            'placeholder' => $this->attr['placeholder'] ?: 'Please choose' . $this->name,
        ],$this->params);

        $data = [
            'nodeName' => 'app-cascader'
        ];

        if(!empty($options)){
            $params['options'] = $options;
        }

        if ($this->route) {
            $data['vBind:dataUrl'] = $this->url;
        } else {
            $data['dataUrl'] = $this->url;
        }

        if ($this->model) {
            $data['vModel:value'] = $this->getModelField();
        }

        $data[$nParamsName] = $params;

        return $data;
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
