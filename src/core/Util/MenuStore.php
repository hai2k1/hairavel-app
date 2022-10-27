<?php

namespace Hairavel\Core\Util;

use Hairavel\Core\UI\Widget\Icon;

/**
 * Menu storage
 */
class MenuStore
{
    private array $data = [];
    private array $pushData = [];
    private array $appData = [];

    private ?string $lastIndex = null;
    private int $lastKey = 0;


    /**
     * Add main menu
     * @param string $index
     * @param array $params
     * @param null $rule
     */
    public function add(string $index, array $params, $rule = null): void
    {
        $this->lastIndex = $index;
        $this->lastKey = 0;
        $this->data[$index] = $params;

        if ($rule instanceof \Closure) {
            $rule($this);
        }
        if (is_string($rule)) {
            $this->data[$index]['route'] = $rule;
        }
    }

    /**
     * Add menu group
     * @param array $params
     * @param callable|null $callback
     * @param null $index
     */
    public function group(array $params, ?callable $callback = null, $index = null): void
    {
        $lastGroup = $this->data[$this->lastIndex];
        if ($index) {
            $lastGroup['menu'][$index] = $params;
        } else {
            $lastGroup['menu'][] = $params;
        }
        $this->data[$this->lastIndex] = $lastGroup;
        $this->lastKey = $index ?: array_key_last($lastGroup['menu']);

        if ($callback instanceof \Closure) {
            $callback($this);
        }
    }


    /**
     * Add menu link
     * @param string $name
     * @param string $route
     * @param array $params
     * @param int $index
     */
    public function link(string $name, string $route, array $params = [], int $index = 0): void
    {
        $data = [
            'name' => $name,
            'route' => $route,
            'params' => $params,
            'order' => $index,
        ];
        $this->data[$this->lastIndex]['menu'][$this->lastKey]['menu'][] = $data;
    }

    /**
     * Add app menu
     * @param array $data
     */
    public function app(array $data): void
    {
        $this->appData[] = $data;
    }

    /**
     * Additional menu
     * @param string $index
     * @param callable $callback
     */
    public function push(string $index, callable $callback): void
    {
        $this->pushData[] = [
            'index' => $index,
            'callback' => $callback
        ];
    }

    /**
     * Get menu data
     * @return array
     */
    public function getData(): array
    {
        foreach ($this->pushData as $vo) {
            $deep = explode('.', $vo['index']);
            $this->lastIndex = $deep[0];
            $this->lastKey = $deep[1] ?: 0;
            $vo['callback']($this);
        }
        return $this->data;
    }

    /**
     * Get app data
     * @return array
     */
    public function getApps(): array
    {
        return $this->appData;
    }

}
