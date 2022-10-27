<?php

namespace Hairavel\Core\UI\Table;

use Hairavel\Core\UI\Tools;
use Hairavel\Core\UI\Widget\Link;
use Hairavel\Core\UI\Widget\Menu;

/**
 * Operation action
 * Class Column
 * @package Hairavel\Core\UI\Filter
 */
class Action
{

    protected array $button = [];
    protected array $menu = [];

    /**
     * button
     * @param string $name
     * @param string $route
     * @param array $params
     * @param string $type
     * @return Link
     */
    public function button(string $name, string $route = '', array $params = [], string $type = 'primary'): Link
    {
        $link = new Link($name, $route, $params);
        $link->button($type);
        $this->button[] = $link;
        return $link;
    }

    /**
     * Menu button
     * @param string $name
     * @param string $type
     * @return Menu
     */
    public function menu(string $name, string $type = 'default'): Menu
    {
        $menu = new Menu($name, $type);
        $this->menu[] = $menu;
        return $menu;
    }

    /**
     * render component
     * @return array
     */
    public function render(): array
    {
        $node = [];
        foreach ($this->menu as $menu) {
            $node[] = $menu->getRender();
        }
        foreach ($this->button as $class) {
            $node[] = $class->getRender();
        }
        return $node;
    }

}
