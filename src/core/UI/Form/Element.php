<?php

namespace Hairavel\Core\UI\Form;

use Hairavel\Core\UI\Tools;

/**
 * Single element component
 * Class Component
 * @package Hairavel\Core\UI
 */
class Element
{
    protected string $name = '';
    protected string $field = '';
    protected string $has = '';
    protected $help = '';
    protected $helpLine = '';
    protected string $prompt = '';
    protected string $model = 'data.';
    protected array $class = [];
    protected $replace = '';
    protected array $attr = [];
    protected array $layoutAttr = [];
    protected array $style = [];
    protected array $pivot = [];
    protected array $verify = [];
    protected array $verifyMsg = [];
    protected array $format = [];
    protected bool $dialog = false;
    protected bool $vertical = false;
    protected bool $label = true;
    protected bool $component = false;
    protected bool $must = false;
    protected array $group = [];
    protected ?int $sort = null;
    protected $modelElo;
    protected $value;
    protected $default;

    /**
     * @var \Closure|null custom callback function (formatting field)
     */
    protected ?\Closure $formatFunc = null;

    /**
     * Set popup
     * @param $bool
     * @return $this
     */
    public function dialog($bool): self
    {
        $this->dialog = $bool;
        return $this;
    }

    /**
     * set the direction
     * @param $bool
     * @return $this
     */
    public function vertical($bool): self
    {
        $this->vertical = $bool;
        return $this;
    }

    /**
     * Set up the data model
     */
    public function modelElo($class)
    {
        $this->modelElo = $class;
        return $this;
    }

    /**
     * set data prefix
     */
    public function model($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Get model fields
     * @return string
     */
    public function getModelField()
    {
        return $this->model . $this->field;
    }

    /**
     * Get the label status
     * @return bool
     */
    public function getLabel(): bool
    {
        return $this->label;
    }


    /**
     * Get field name
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Get the associated model
     * @return string
     */
    public function getHas(): string
    {
        return $this->has;
    }

    /**
     * get name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * get value
     * @param $value
     * @return mixed
     */
    public function getValue($value = null)
    {
        return ($this->value ?? $value) ?? $this->default;
    }

    /**
     * get array value
     * @param $value
     * @param bool $json
     * @return array|null
     */
    public function getValueArray($value, bool $json = false): ?array
    {
        $value = $this->getValue($value);
        if ($value instanceof \Illuminate\Database\Eloquent\Collection) {
            if ($value->count()) {
                $values = $value->pluck($value->first()->getKeyName())->toArray();
            } else {
                $values = $json ? [] : null;
            }
        } else if (is_array($value)) {
            $values = $value;
        } else if ($value !== null) {
            $values = $json ? json_decode($value, true) : explode(',', $value);
        } else {
            $values = $json ? [] : null;
        }
        return $values;
    }

    /**
     * Get callback data set
     * @param $data
     * @param $value
     * @return array
     */
    public function getCallbackArray($data, $value): array
    {
        if ($data instanceof \Closure) {
            return call_user_func($data, [$value]);
        }
        if (is_array($data)) {
            return $data;
        }
        return [];
    }

    /**
     * set option value
     * @param $value
     * @return $this
     */
    public function value($value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * set default value
     * @param $value
     * @return $this
     */
    public function default($value): self
    {
        $this->default = $value;
        return $this;
    }

    /**
     * Set up help information
     * @param string|array $value
     * @param bool $line
     * @return $this
     */
    public function help($value, bool $line = false): self
    {
        if ($line) {
            $this->helpLine = $value;
        } else {
            $this->help = $value;
        }
        return $this;
    }

    /**
     * attribute data
     * @param string $name
     * @param string|array $value
     * @return $this
     */
    public function attr(string $name, $value): self
    {
        $this->attr[$name] = $value;
        return $this;
    }

    /**
     * Layout tree
     * @param string $name
     * @param $value
     * @return $this
     */
    public function layoutAttr(string $name, $value): self
    {
        $this->layoutAttr[$name] = $value;
        return $this;
    }

    /**
     * attribute array
     * @param $attr
     * @return $this
     */
    public function attrArray($attr): self
    {
        $this->attr = $attr;
        return $this;
    }

    /**
     * class style
     * @param string $name
     * @return $this
     */
    public function class(string $name): self
    {
        $this->class[] = $name;
        return $this;
    }

    /**
     * String replacement label (used for numeric string processing)
     * @param $replace
     * @return $this
     */
    public function replace($replace): self
    {
        $this->replace = $replace;
        return $this;
    }

    /**
     * Setting tips
     * @param $name
     * @return $this
     */
    public function placeholder($name): self
    {
        if ($name) {
            $this->attr['placeholder'] = $name;
        }
        return $this;
    }

    /**
     * element grouping
     * @param $name
     * @param $value
     * @return $this
     */
    public function group($name, $value): self
    {
        $this->group[] = [
            'name' => $name,
            'value' => $value
        ];
        return $this;
    }

    /**
     * Required style
     * @return $this
     */
    public function must(): self
    {
        $this->must = true;
        $this->verify['all'][$this->field][] = 'required';
        $this->verifyMsg['all'][$this->field . '.' . 'required'] = 'Please enter' . $this->name;
        return $this;
    }

    /**
     * help information
     * @param $content
     * @return $this
     */
    public function prompt($content): self
    {
        $this->prompt = $content;
        return $this;
    }

    /**
     * sort
     * @param $num
     * @return $this
     */
    public function sort($num): self
    {
        $this->sort = $num;
        return $this;
    }

    /**
     * get group
     * @return array
     */
    public function getGroup(): array
    {
        return $this->group;
    }

    /**
     * Get must
     * @return bool
     */
    public function getMust(): bool
    {
        return $this->must;
    }

    /**
     * Get reminders
     * @return string
     */
    public function getPrompt(): string
    {
        return $this->prompt;
    }

    /**
     * get help line
     * @return string|array
     */
    public function getHelpLine()
    {
        return $this->helpLine;
    }


    /**
     * Get layer properties
     * @return array
     */
    public function getLayoutAttr()
    {
        return $this->layoutAttr;
    }

    /**
     * Sync additional data
     * @param $data
     * @return $this
     */
    public function pivot($data)
    {
        $this->pivot = $data;
        return $this;
    }

    /**
     * Set field validation
     * @param $rule
     * @param array $msg
     * @param string $time
     * @return $this
     */
    public function verify($rule, array $msg = [], string $time = 'all'): self
    {
        $this->verify[$time][$this->field] = $rule;
        foreach ($msg as $key => $vo) {
            $this->verifyMsg[$time][$this->field . '.' . $key] = $vo;
        }
        return $this;
    }

    /**
     * Get validation rules
     * @param string $time
     * @return array
     */
    public function getVerify(string $time = 'add'): array
    {
        return [
            'rule' => (array)$this->verify['all'] + (array)$this->verify[$time],
            'msg' => (array)$this->verifyMsg['all'] + (array)$this->verifyMsg[$time]
        ];
    }

    /**
     * Set form formatting
     * @param string|callable $rule
     * @param string $time
     * @return $this
     */
    public function format($rule, string $time = 'all'): self
    {
        $this->format[$time][] = $rule;
        return $this;
    }

    /**
     * Get form formatting
     * @param string $time
     * @return array
     */
    public function getFormat(string $time = 'add'): array
    {
        return (array)$this->format['all'] + (array)$this->format[$time];
    }

    /**
     * Custom callback function
     * (formatted field, suitable for reading data across fields)
     *e.g.
     * $form->text('URL', 'url')->custom(function ($info) {
     * return $info->scheme . '://' . $info->url;
     * });
     *
     * @param \Closure $func
     * @return $this
     */
    public function custom(\Closure $func)
    {
        $this->formatFunc = $func;
        return $this;
    }

    /**
     * Get submission data
     * @param string $time
     * @return mixed
     */
    public function getInput(string $time = 'add'): array
    {
        $data = request()->input($this->field);
        $inputs = [];
        if (method_exists($this, 'appendInput') && !$this->has) {
            $appendData = $this->appendInput($data);
            foreach ($appendData as $key => $vo) {
                $inputs[$key] = ['value' => $vo];
            }
        }

        if (method_exists($this, 'dataInput') && !$this->has) {
            $data = $this->dataInput($data);
        }
        $inputs[$this->field] = ['value' => $data, 'has' => $this->has, 'format' => $this->getFormat($time), 'verify' => $this->getVerify($time), 'pivot' => $this->pivot];

        return $inputs;
    }

    /**
     * Get help information
     * @return string|array
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Get order
     * @return null
     */
    public function getSort(): ?int
    {
        return $this->sort;
    }

    /**
     * Composite components
     * @return bool
     */
    public function getComponent(): bool
    {
        return $this->component;
    }

    /**
     * Get the rendering component
     * @return array
     */
    public function getRender(): array
    {
        if ($this->class) {
            $this->attr['class'] = implode(' ', $this->class);
        }
        return array_merge($this->render(), $this->attr);
    }


    /**
     * Get data value
     * @param $info
     * @return array
     */
    public function getData($info): array
    {
        $field = $this->getHas() ?: $this->getField();
        $value = Tools::parsingArrData($info, $field);

        $data = [];
        if (method_exists($this, 'appendValue')) {
            $appendValue = $this->appendValue($info);
            foreach ($appendValue as $key => $vo) {
                $data[$key] = $vo;
            }
        }
        if (method_exists($this, 'dataValue')) {
            $value = $this->dataValue($value, $info);
        } else {
            $value = $this->getValue($value);
        }

        // custom callback function (formatting field)
        if ($this->formatFunc && is_callable($this->formatFunc)) {
            $value = call_user_func($this->formatFunc, $info);
        }

        $data[$this->getField()] = $value;

        return $data;
    }


}
