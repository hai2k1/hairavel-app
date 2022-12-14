<?php

namespace Hairavel\Core\UI\Table\Column;

use Hairavel\Core\UI\Tools;

/**
 * Class Menu
 */
class Menu implements Component
{

    private array $link;
    private array $routes = [];

    /**
     * add entry
     * @param string $name
     * @param string $route
     * @param array $params
     * @return \Hairavel\Core\UI\Widget\Link
     */
    public function add(string $name, string $route, array $params = []): \Hairavel\Core\UI\Widget\Link
    {
        $label = $route . '?' . http_build_query($params);
        $link = new \Hairavel\Core\UI\Widget\Link($name, $route, $params);
        $link = $link->model('rowData');
        $this->link[] = $link;
        $this->routes[$label] = [
            'route' => $route,
            'params' => $params
        ];
        return $link;
    }

    /**
     * retrieve data
     * @param $rowData
     * @return array
     */
    public function getData($rowData): array
    {
        $urls = [];
        foreach ($this->routes as $key => $vo) {
            $params = [];
            foreach ($vo['params'] as $k => $v) {
                $params[$k] = Tools::parsingArrData($rowData, $v, true);
            }
            $urls[$key] = route($vo['route'], $params, false);
        }
        return $urls;
    }

    /**
     * @param $label
     * @return array
     */
    public function render($label): array
    {
        $options = [];
        foreach ($this->link as $key => $class) {
            $data = $class->render();

            $route = [
                'nodeName' => 'route',
                'type' => $data['type'],
                'title' => $data['title'],
                'href' => $class->getRoute(),
            ];

            $options[] = [
                'label' => $data['name'],
                'key' => $key,
                'route' => $route,
            ];
        }
        $options = array_filter($options);

        return [
            'nodeName' => 'n-dropdown',
            'width' => '80',
            'placement' => 'right-start',
            'overlap' => true,
            'trigger' => 'click',
            'options' => $options,
            'render-label:option' => [
                'nodeName' => 'route',
                'class' => 'block',
                'vBind:href' => 'rowData.record[option.route.href]',
                'vBind:title' => 'option.route.title',
                'vBind:type' => 'option.route.type',
                'child' => '{{option.label}}'
            ],
            'child' => [
                'nodeName' => 'n-icon',
                'class' => 'cursor-pointer',
                'size' => 16,
                'child' => [
                    'nodeName' => 'dots-vertical-icon'
                ]
            ],
        ];
    }

}
