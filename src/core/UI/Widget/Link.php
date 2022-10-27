<?php

namespace Hairavel\Core\UI\Widget;

use Hairavel\Core\Facades\Permission;
use Hairavel\Core\UI\Tools;
use Hairavel\Core\UI\Widget\Append\Element;

/**
 * link component
 *Class Link
 * @package Hairavel\Core\UI\Widget
 */
class Link extends Widget
{
    use Element;

    protected string $name;
    protected string $route;
    protected string $url;
    protected bool $absolute = false;
    protected array $params = [];
    protected array $fields = [];
    protected ?\Closure $show = null;
    protected string $button = '';
    protected string $type = 'default';
    protected string $status = 'normal';
    protected bool $block = false;
    protected string $model = '';
    protected array $data = [];
    protected array $class = [];
    protected array $typeConfig = [];
    protected string $icon = '';
    protected string $auth = '';
    protected string $urlQuery = "";
    protected string $bindFilter = 'data.filter';

    /**
     * @param string $name
     * @param string $route
     * @param array $params
     * @param bool $absolute
     */
    public function __construct(string $name, string $route = '', array $params = [], bool $absolute = false)
    {
        $this->name = $name;
        $this->route = $route;
        $this->params = $params ?: [];
        $this->absolute = $absolute;
    }

    /**
     * @param $params
     * @return $this
     */
    public function fields($params): self
    {
        $this->fields = $params;
        return $this;
    }

    /**
     * link type
     * @param string $name
     * @param array $config
     * @return $this
     */
    public function type(string $name = 'default', array $config = []): self
    {
        $this->type = $name;
        $this->typeConfig = $config;
        return $this;
    }

    /**
     * get type
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get type configuration
     * @return array
     */
    public function getTypeConfig(): array
    {
        return $this->typeConfig;
    }

    /**
     * data model
     * @param string $model
     * @return $this
     */
    public function model(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * icon
     * @param $icon
     * @return $this
     */
    public function icon($icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * button properties
     * @param string $type
     * @param string $status
     * @param bool $block
     * @return $this
     */
    public function button(string $type = 'primary', string $status = 'medium', bool $block = false): self
    {
        $this->button = $type;
        $this->status = $status;
        $this->block = $block;
        return $this;
    }

    /**
     * get route
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
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
     * Set filter binding data
     * @param string $filterName
     * @param string $name
     * @return $this
     */
    public function bindFilter(string $filterName,string $name = 'filter'): self
    {
        $this->bindFilter = "{$filterName}.{$name}";
        return $this;
    }

    /**
     * Custom permissions
     * @param string $name
     * @return $this
     */
    public function can(string $name): self
    {
        if (strpos($name, '.') !== false) {
            $this->auth = $name;
        } else {
            $this->auth = $this->route . '|' . $name;
        }
        return $this;
    }

    public function urlJoin(string $query): self
    {
        $this->urlQuery = $query;
        return $this;
    }

    /**
     * get url
     * @return false|string
     */
    public function getUrl()
    {
        if (!$this->isAuth()) {
            return false;
        }

        if ($this->show && !call_user_func($this->show)) {
            return false;
        }

        $url = app_route($this->route, $this->params, $this->absolute, $this->model, $this->fields);
        if ($this->urlQuery) {
            if (strpos($url, "?") !== -1) {
                $url .= "&".$this->urlQuery;
            }else {
                $url .= "?".$this->urlQuery;
            }
        }
        return $url;
    }

    /**
     * @return array
     */
    public function render(): array
    {
        $url = $this->getUrl();

        if (!$url) {
            return [];
        }

        $object = [
            'nodeName' => 'route',
        ];

        switch ($this->type) {
            case 'default':
                $object['vBind:href'] = $url;
                break;
            case 'blank':
                $object['nodeName'] = 'a';
                $object['vBind:href'] = $url;
                $object['target'] = '_blank';
                $object['child'] = $this->name;
                break;
            case 'dialog':
                $object['vBind:href'] = $url;
                $object['type'] = 'dialog';
                $object['title'] = $this->name;
                break;
            case 'drawer':
                $object['vBind:href'] = $url;
                $object['type'] = 'dialog';
                $object['mode'] = 'drawer';
                $object['title'] = $this->name;
                break;
            case 'ajax':
                $object['vBind:href'] = $url;
                $object['type'] = 'ajax';
                $object['title'] = 'Confirm to proceed' . $this->name . 'Operation?';
                break;
        }

        $object = array_merge($object, $this->typeConfig);

        if ($this->type == 'download') {
            $link = [
                'nodeName' => 'a-button',
                'class' => implode(' ', $this->class),
                'type' => 'primary',
                'status' => $this->status,
                'vOn:click' => "dux.request.download(" . $url . "+'?'+dux.qs.stringify({$this->bindFilter}),'absolute')",
                'child' => [
                    $this->name
                ]
            ];
            if ($this->icon) {
                $link['child'][] = (new Icon($this->icon))->attr('vSlot:icon', '')->getRender();
            }
            if ($this->block) {
                $link['long'] = true;
            }
            return $link;
        }else if ($this->button) {
            $link = [
                'nodeName' => 'a-button',
                'class' => implode(' ', $this->class),
                'type' => $this->button,
                'status' => $this->status,
                'child' => [
                    $this->name
                ]
            ];
            if ($this->icon) {
                $link['child'][] = (new Icon($this->icon))->attr('vSlot:icon', '')->getRender();
            }
            if ($this->block) {
                $link['long'] = true;
            }
        } else {
            $link = [
                'nodeName' => 'span',
                'class' => 'arco-link arco-link-status-normal ' . implode(' ', $this->class),
                'child' => [
                    $this->name
                ]
            ];
            if ($this->icon) {
                $link['child'][] = (new Icon($this->icon))->class('mr-2')->getRender();
            }
        }

        $object['child'] = $link;

        return array_merge($object, $this->attr);
    }

    private function isAuth(): bool
    {
        // route does not exist
        if (!\Route::has($this->route)) {
            return false;
        }
        // Check if the class is public
        $public = \Route::getRoutes()->getByName($this->route)->getAction('public');
        if ($public) {
            return true;
        }
        // Verify if the current daemon
        $app = \Str::before($this->route, '.');
        if ($app <> Permission::getGuerd()) {
            return true;
        }

        // Set general page permissions
        if (\Str::afterLast($this->route, '.') === 'page') {
            if ($this->params['id']) {
                $this->can('edit');
            } else {
                $this->can('add');
            }
        }

        // Verify custom permissions
        if ($this->auth) {
            if (!auth($app)->user()->can($this->auth)) {
                return false;
            }
            return true;
        }

        // Verify general permissions
        if (auth($app)->user()->can($this->route)) {
            return true;
        }
        return false;
    }

}
