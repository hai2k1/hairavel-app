<?php

namespace Hairavel\Core\UI\Widget;

use Hairavel\Core\UI\Widget;

/**
 * Description list Descriptions
 *
 * @see [arco.design] https://arco.design/vue/component/descriptions
 * @package Hairavel\Core\UI\Widget
 */
class Descriptions extends Widget
{

    /**
     * @var string title (optional)
     */
    private string $title = '';

    /**
     * @var string the number of data to put in each line (optional)
     * The format is Number: 3
     * The format is Grid: {xs:1, md:3, lg:4}
     */
    private string $column = '';

    /**
     * @var string describing the arrangement of the list (optional)
     * Supported values: 'horizontal' | 'vertical' | 'inline-horizontal' | 'inline-vertical'
     */
    private string $layout = '';

    /**
     * @var string Label or text alignment (optional)
     * Supported alignments: left, center, right
     * Format①: Only set text is valid, "right"
     * Format ②: Both label and text are acceptable, "{label: 'left', value: 'right'}"
     */
    private string $align = '';

    /**
     * @var string describing the size of the list (optional)
     * Supported values: 'mini' | 'small' | 'medium' | 'large'
     */
    private string $size = '';

    /**
     * @var bool whether to show border (optional)
     */
    private ?bool $bordered = false;

    /**
     * @var array data, each item contains two fields, label and value
     * Format:
     * [
     * ['label' => 'Text', 'value' => 'desc...'],
     * ['label' => 'Icon', 'value' => Widget::icon('archive')],
     * ['label' => 'Bind', 'value' => '{{rowData.id}}'],
     * ]
     */
    private array $data = [];

    /**
     * @param array $data
     * @param callable|null $callback
     */
    public function __construct(array $data = [], callable $callback = null)
    {
        $this->add(...$data);
        $this->callback = $callback;
    }

    /**
     * add data item
     *
     * @param array ...$data One or more pieces of data, see the $this->data annotation for the format
     * @return $this
     */
    public function add(array...$data)
    {
        foreach ($data as $item) {
            if (isset($item['label']) && isset($item['value'])) {
                $this->data[] = $item;
            }
        }
        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function title(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function column(string $column)
    {
        $this->column = $column;
        return $this;
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function layout(string $layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @param string $align
     * @return $this
     */
    public function align(string $align)
    {
        $this->align = $align;
        return $this;
    }

    /**
     * @param string $size
     * @return $this
     */
    public function size(string $size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @param bool $bordered
     * @return $this
     */
    public function bordered(?bool $bordered)
    {
        $this->bordered = $bordered;
        return $this;
    }

    public function render(): array
    {
        if (empty($this->data)) {
            return [
                'nodeName' => 'a-empty',
            ];
        }

        $node = [
            'nodeName' => 'a-descriptions',
        ];
        $this->title && $node['title'] = $this->title;
        $this->column && $node['column'] = $this->column;
        $this->layout && $node['layout'] = $this->layout;
        $this->size && $node['size'] = $this->size;
        is_bool($this->bordered) && $node['bordered'] = $this->bordered;

        if ($this->align) {
            if (in_array($this->align, ['left', 'center', 'right'])) {
                $node['align'] = $this->align;
            } else {
                $node['vBind:align'] = $this->align;
            }
        }

        // Do not pass in data items, but pass in data items in child mode, so that value supports Node nodes and supports row data field binding {{rowData.id}}
        foreach ($this->data as $item) {
            $node['child'][] = [
                'nodeName' => 'a-descriptions-item',
                'label' => $item['label'],
                'child' => $item['value'],
            ];
        }

        return $node;
    }

}
