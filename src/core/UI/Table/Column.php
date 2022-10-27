<?php

namespace Hairavel\Core\UI\Table;

use Hairavel\Core\UI\Table;
use Hairavel\Core\UI\Tools;
use Hairavel\Core\UI\Widget\Link;
use Exception;

/**
 * table column
 * The following Method returns $this to be consistent with the value returned by __call(), which is convenient for IDE identification
 * @method $this hidden() Column\Hidden
 * @method $this progress(string $color = 'default') Column\Progress
 * @method $this status(array $map, array $color, string $type = 'badge') Column\Status
 * @method $this chart(int $day = 7, string $has = 'viewsData', string $key = 'pv', string $name = 'views', string $type = 'area') Column\Chart
 * @method $this tags(array $map, array $color) Column\Tags
 * @method $this toggle(string $field, string $url, array $params = []) Column\Toggle
 * @method $this input(string $field, $url, array $params = []) Column\Input
 *
 * @package Hairavel\Core\UI\Table
 */
class Column
{

    protected string $name;
    protected string $label = '';
    protected ?\Closure $callback = null;
    protected array $node = [];
    protected array $attr = [];
    protected array $function = [];
    protected array $children = [];
    protected $width = '';
    protected $align = '';
    protected $fixed = '';
    protected $class = [];
    protected $replace = '';
    protected ?int $colspan = null;
    protected ?\Closure $show = null;
    protected ?int $sort = null;
    protected $sorter = null;
    protected $layout;
    protected $relation;
    protected $model;
    protected $element;
    protected $extend;

    /**
     * Column constructor.
     * @param string $name
     * @param string $label
     * @param null $callback
     */
    public function __construct(string $name = '', string $label = '', $callback = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->callback = $callback;
    }

    /**
     * Set the parent object
     * @param Table $layout
     */
    public function setLayout(Table $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Linked data
     * @param $relation
     * @return $this
     */
    public function setRelation($relation): self
    {
        $this->relation = $relation;
        return $this;
    }

    /**
     * width
     * @param $width
     * @return $this
     */
    public function width($width): self
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Custom node data
     * @param $node
     * @return $this
     */
    public function node($node): self
    {
        $this->node = $node;
        return $this;
    }

    /**
     * align
     * @param string $align
     * @return $this
     * @throws Exception
     */
    public function align(string $align): self
    {
        $this->align = $align;
        return $this;
    }

    /**
     * Fixed column
     * @param string $fixed
     * @return $this
     */
    public function fixed(string $fixed = 'right'): self
    {
        $this->fixed = $fixed;
        return $this;
    }

    /**
     * Set style class
     * @param string $class
     * @return $this
     */
    public function class(string $class): self
    {
        $this->class[] = $class;
        return $this;
    }

    /**
     * Set additional properties
     * @param string $name
     * @param $value
     * @return $this
     */
    public function attr(string $name, $value): self
    {
        $this->attr[$name] = $value;
        return $this;
    }

    /**
     * set color
     * @param string $name
     * @return $this
     */
    public function color(string $name): self
    {
        $this->class[] = 'text-' . $name;
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
     * Add a link
     * @param string $name
     * @param string $route
     * @param array $params
     * @param bool $absolute
     * @return Link
     */
    public function link(string $name, string $route, array $params = [], bool $absolute = false): Link
    {
        if (!$this->element) {
            $this->element = new Table\Column\Link();
            $this->element->fields($this->layout->fields);
        }
        return $this->element->add($name, $route, $params, $absolute);
    }

    /**
     * add menu
     * @param string $name
     * @param string $route
     * @param array $params
     * @return Link
     */
    public function menu(string $name, string $route, array $params = []): Link
    {
        if (!$this->element) {
            $this->element = new Table\Column\Menu();
        }
        return $this->element->add($name, $route, $params);
    }

    /**
     * subtitle
     * @param string $label
     * @param callable|null $callback
     * @return $this
     */
    public function desc(string $label, callable $callback = null): self
    {
        if (!$this->element && !$this->element instanceof Table\Column\RichText) {
            $this->element = new Table\Column\RichText();
            $this->element->setRelation($this->relation);
        }
        $this->element->desc($label, $callback);
        return $this;
    }

    /**
     * picture display
     * @param string $label
     * @param callable|null $callback
     * @param int $width
     * @param int $height
     * @param string $placeholder
     * @return $this
     */
    public function image(string $label, callable $callback = null, int $width = 10, int $height = 10, string $placeholder = ''): self
    {
        if (!$this->element && !$this->element instanceof Table\Column\RichText) {
            $this->element = new Table\Column\RichText();
        }
        $this->element->image($label, $width, $height, $placeholder, $callback);
        return $this;
    }

    /**
     * format time
     * @param $format
     * @return $this
     */
    public function date($format): self
    {
        $this->function[] = [
            'fun' => 'date',
            'params' => $format
        ];
        return $this;
    }

    /**
     * show hide
     * @param callable $callback
     * @return $this
     */
    public function show(callable $callback): self
    {
        $this->show = $callback;
        return $this;
    }

    /**
     * Column sort
     * @param int $num
     * @return $this
     */
    public function sort(int $num): self
    {
        $this->sort = $num;
        return $this;
    }

    /**
     * Sort condition
     *
     * Example ①: This column supports sorting, and the sorting field is the current table field (virtual columns are not supported)
     * $table->column(...)->sorter(true);
     * <br/>
     * Example ②: This column supports sorting, and the sorting field is the actual parameter value (id here)
     * $table->column(...)->sorter('id');
     * <br/>
     * Example ③: The column supports sorting, the sorting field is the actual parameter value (id here), and the column is used as the default sorting (the default is in the reverse order of desc)
     * $table->column(...)->sorter('id desc');
     * <br/>
     * Example ④: This column supports sorting, and sorting is processed according to the closure callback (you need to call orderBy yourself)
     * $table->column(...)->sorter(function (ModelAgent $query, $value) {
     * $query->orderBy('id', $value == 'asc' ? 'asc' : 'desc');
     * });
     * <br/>
     * Example ⑤: Clear the previously set sorting of the column, set it to false
     * $table->column(...)->sorter(...)->sorter(false);
     *
     * @param string|bool|\Closure field ordering
     */
    public function sorter($sorter = true): self
    {
        $this->sorter = $sorter;
        return $this;
    }

    /**
     * Column merge
     * @param int $num
     * @return $this
     */
    public function colspan(int $num): self
    {
        $this->colspan = $num;
        return $this;
    }

    // group table
    public function children(string $name = '', string $label = '', $callback = null): self
    {
        $this->children[] = new Column($name, $label, $callback);
        return $this;
    }

    /**
     * Get field name
     * @return string
     */
    public function getLabel(): string
    {
        return Tools::converLabel($this->label, $this->relation);
    }

    /**
     * Get column configuration
     */
    public function getRender(): array
    {
        $render = $this->node;
        if ($this->node instanceof \Closure) {
            $render = call_user_func($this->node);
        }
        if ($this->element) {
            $render = $this->element->render($this->getLabel());
        }

        $node = [
            'title' => $this->name,
            'dataIndex' => $this->getLabel(),
            'width' => $this->width,
            'className' => implode(' ', $this->class),
            'colSpan' => $this->colspan,
            'sort' => $this->sort,
            'align' => $this->align,
        ];

        if ($this->fixed) {
            $node['fixed'] = $this->fixed;
        }

        if($this->replace){
            $node['replace'] = $this->replace;
        }

        if ($this->children) {
            $children = [];
            foreach ($this->children as $item) {
                $children[] = $item->getRender();
            }
            $node['children'] = $children;
        }

        if ($this->sorter) {
            $node['vBind:sortable'] = 'colSortable';
        }

        if ($render) {
            $node['render:rowData, rowIndex'] = $render;
        }

        return array_merge($node, $this->attr);
    }

    /**
     * row data
     * @param $rowData
     * @return array
     */
    public function getData($rowData): array
    {
        if ($this->relation) {
            // Parse associative array
            $parsingData = Tools::parsingObjData($rowData, $this->relation, $this->label);
        } else {
            // Parse a normal array
            $parsingData = Tools::parsingArrData($rowData, $this->label);
        }

        // callback handler
        if ($this->callback instanceof \Closure) {
            $callback = call_user_func($this->callback, $parsingData, $rowData);
            if ($callback) {
                $parsingData = $callback;
            }
        } else {
            $parsingData = $this->callback ?: $parsingData;
        }

        // function processing
        if ($this->function) {
            foreach ($this->function as $vo) {
                if (function_exists($vo['fun'])) {
                    $parsingData = call_user_func($vo['fun'], $vo['params'], $parsingData);
                }
            }
        }

        if ($this->label) {
            $data = [
                $this->getLabel() => $parsingData
            ];
        } else {
            $data = [];
        }

        // element data
        if ($this->element && method_exists($this->element, 'getData')) {
            $data = array_merge($data, $this->element->getData($rowData, $this->getLabel(), $parsingData));
        }

        if ($this->children) {
            foreach ($this->children as $item) {
                $data = array_merge($data, $item->getData($rowData));
            }
        }

        return $data;
    }

    /**
     * column condition
     * @param $query
     * @return false|void
     */
    public function execute($query)
    {
        // column sort
        $sort = request()->get('_sort');
        // sort by
        $value = $sort && $sort[$this->label] ? $sort[$this->label] : null;
        // sorter is not empty to indicate that field sorting is required
        if (!$this->sorter) {
            return false;
        }
        // sorter is a closure, indicating that the sorting has been customized
        if ($this->sorter instanceof \Closure) {
            // Closure custom processing sorting, pay attention to sorting type is NULL and illegal value
            call_user_func($this->sorter, $query, $value);
        }
        // sorter is the field name, indicating the custom sorting field name
        // sorter is another value, indicating that sorting is enabled, and label is used as the sorting field
        else {
            $field = $this->label; // defaults to label
            // When sorter is a string, it is used as a sort field
            if (is_string($this->sorter)) {
                $field = $this->sorter;
                // sorter contains the sort type (including spaces)
                if (stripos($this->sorter, ' ') !== false) {
                    [$field, $value] = explode(' ', $this->sorter);
                    $field = trim($field);
                    $value = trim($value);
                }
            }
            // If not passed or the sort type is not desc, the default is asc ascending order
            $query->orderBy($field, $value === 'desc' ? 'desc' : 'asc');
        }
    }

    /**
     * Perform element processing
     * @param callable $callback
     * @return $this
     */
    public function element(callable $callback){
        $callback($this->element);
        return $this;
    }

    /**
     * @param $method
     * @param $arguments
     * @return $this
     * @throws Exception
     */
    public function __call($method, $arguments)
    {
        $class = '\\Hairavel\\Core\\UI\\Table\\Column\\' .ucfirst($method);
        if (!class_exists($class)) {
            if (!$this->extend[$method]) {
                throw new \Exception('There is no form method "' . $method . '"');
            } else {
                $class = $this->extend[$method];
            }
        }
        $object = new $class(...$arguments);
        if (method_exists($object, 'fields')) {
            $object->fields($this->layout->fields);
        }
        $this->element = $object;
        // The Column itself is returned here, so the call to the component requires ->element(callback)
        return $this;
    }

}
