<?php

namespace Hairavel\Core\UI\Table;

use Hairavel\Core\UI\Form\Cascader;
use Hairavel\Core\UI\Form\Date;
use Hairavel\Core\UI\Form\Daterange;
use Hairavel\Core\UI\Form\Datetime;
use Hairavel\Core\UI\Form\Select;
use Hairavel\Core\UI\Form\Text;
use Hairavel\Core\UI\Table;

/**
 * table filter
 * Class Column
 * @package Hairavel\Core\UI\Filter
 */
class Filter
{

    protected string $name;
    protected string $field;
    protected $default;
    protected $where = true;
    protected Table $layout;
    protected string $type = '';
    protected string $placeholder = '';
    protected bool $quick = false;
    protected string $condition = '';
    protected ?\Closure $callback;
    protected $data;
    protected $model;
    protected $value;

    /**
     * Filter constructor.
     * @param string $name
     * @param string $field
     * @param bool $where
     * @param null $default
     */
    public function __construct(string $name, string $field, $where = true, $default = null)
    {
        $this->name = $name;
        $this->field = $field;
        $this->where = $where;
        $this->default = $default;
        $this->value = request()->get($field, $this->default);
    }

    /**
     * Set the parent object
     * @param Table $layout
     */
    public function setLayout(Table $layout): void
    {
        $this->layout = $layout;
        $this->model = $layout->model();
    }

    /**
     * Cascade option
     * @param callable|array $data
     * @param callable|null $callback
     * @return $this
     */
    public function cascader($data = [], callable $callback = NULL): self
    {
        $this->data = $data;
        $this->type = 'cascader';
        $this->callback = $callback;
        return $this;
    }

    /**
     * drop down box
     * @param callable|array $data
     * @param callable|null $callback
     * @return $this
     */
    public function select($data = [], callable $callback = NULL): self
    {
        $this->data = $data;
        $this->type = 'select';
        $this->callback = $callback;
        return $this;
    }

    /**
     * Text library
     * @param string $placeholder
     * @param callable|null $callback
     * @return $this
     */
    public function text(string $placeholder = '', callable $callback = NULL): self
    {
        $this->placeholder = $placeholder;
        $this->type = 'text';
        $this->callback = $callback;
        return $this;
    }

    /**
     * date
     * @param string $placeholder
     * @param callable|null $callback
     * @return $this
     */
    public function date(string $placeholder = '', callable $callback = NULL): self
    {
        $this->placeholder = $placeholder;
        $this->type = 'date';
        $this->callback = $callback;
        return $this;
    }

    /**
     * date time
     * @param string $placeholder
     * @param callable|null $callback
     * @return $this
     */
    public function datetime(string $placeholder = '', callable $callback = NULL): self
    {
        $this->placeholder = $placeholder;
        $this->type = 'datetime';
        $this->callback = $callback;
        return $this;
    }

    /**
     * date range
     * @param string $placeholder
     * @param callable|null $callback
     * @return $this
     */
    public function daterange(string $placeholder = '', callable $callback = NULL): self
    {
        $this->placeholder = $placeholder;
        $this->type = 'daterange';
        $this->callback = $callback;
        return $this;
    }

    /**
     * Quick filter
     * @return $this
     */
    public function quick(): self
    {
        $this->quick = true;
        return $this;
    }

    /**
     * filter criteria
     * @param $type
     * @return $this
     */
    public function condition($type): self
    {
        $this->condition = $type;
        return $this;
    }


    /**
     * perform filter
     * @param $query
     * @return false
     */
    public function execute($query): bool
    {
        if ($this->value === null) {
            return false;
        }
        if (is_array($this->value) && empty($this->value)) {
            return false;
        }
        if ($this->where instanceof \Closure) {
            call_user_func($this->where, $query, $this->value, $this->data);
        } elseif ($this->where !== false) {

            $field = is_string($this->where) ? $this->where : $this->field;
            $condition = '=';
            $value = $this->value;

            if ($this->condition === 'like') {
                $condition = 'like';
                $value = '%' . $value . '%';
            }

            $query->where($field, $condition, $value);
        }
        return true;
    }

    /**
     * render component
     * @return array
     */
    public function render(): array
    {
        if (!$this->type) {
            $this->layout->filterParams($this->field, $this->value);
            return [];
        }
        switch ($this->type) {
            case 'select':
                $object = new Select($this->name, $this->field, $this->data);
                $object->tip(true);
                break;
            case 'cascader':
                $object = new Cascader($this->name, $this->field, $this->data);
                break;
            case 'date':
                $object = new Date($this->name, $this->field);
                break;
            case 'datetime':
                $object = new Datetime($this->name, $this->field);
                break;
            case 'daterange':
                $object = new Daterange($this->name, $this->field);
                break;
            case 'text':
            default:
                $object = new Text($this->name, $this->field);
        }

        $object->model('data.filter.');


        $this->layout->filterParams($this->field, $this->value);

        if ($this->callback instanceof \Closure) {
            call_user_func($this->callback, $object);
        }

        $data = [
            'status' => $this->value !== null,
            'quick' => $this->quick,
            'where' => $this->where,
            'value' => $this->value,
            'field' => $this->field,
            'data' => $this->data,
            'name' => $this->name
        ];

        if ($this->quick) {
            $data['render'] = [
                'nodeName' => 'div',
                'class' => 'lg:w-40',
                'child' => $object->placeholder($this->placeholder)->getRender()
            ];
        } else {
            $data['render'] = [
                'nodeName' => 'div',
                'class' => 'my-2',
                'child' => [
                    [
                        'nodeName' => 'div',
                        'child' => $this->name,
                    ],
                    [
                        'nodeName' => 'div',
                        'class' => 'mt-2',
                        'child' => $object->placeholder($this->placeholder)->getRender()
                    ]
                ],
            ];
        }

        return $data;
    }
}
