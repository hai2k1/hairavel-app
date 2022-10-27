<?php

namespace Hairavel\Core\UI\Table;

use Closure;
use Hairavel\Core\Util\Excel;
use Hairavel\Core\UI\Tools;

/**
 * Data output
 * Class Column
 * @package Hairavel\Core\UI\Filter
 */
class Export
{
    public array $column = [];
    public string $title = 'Data Export';
    public string $subtitle = '';

    /**
     * title
     * @param $name
     * @return $this
     */
    public function title($name): self
    {
        $this->title = $name;
        return $this;
    }

    /**
     * subtitle
     * @param $name
     * @return $this
     */
    public function subtitle($name): self
    {
        $this->subtitle = $name;
        return $this;
    }

    /**
     * Column settings
     * @param string $name
     * @param string|Closure $value
     * @param int $width
     * @return $this
     */
    public function column(string $name, $value, int $width = 10): self
    {
        $this->column[] = [
            'name' => $name,
            'value' => $value,
            'width' => $width
        ];
        return $this;
    }

    /**
     * output form
     * @param $data
     */
    public function render($data): void
    {
        $header = [];
        $cellData = [];
        foreach ($data as $vo) {
            $tmp = [];
            foreach ($this->column as $column) {
                if (is_string($column['value'])) {
                    $tmp[] = Tools::parsingArrData($vo, $column['value']);
                } else {
                    $tmp[] = call_user_func($column['value'], $vo);
                }
            }
            $cellData[] = $tmp;
        }
        foreach ($this->column as $vo) {
            $header[] = [
                'name' => $vo['name'],
                'width' => $vo['width']
            ];
        }
        Excel::export($this->title, $this->subtitle, $header, $cellData);
    }

}
