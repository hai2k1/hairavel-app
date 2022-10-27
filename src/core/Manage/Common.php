<?php

namespace Hairavel\Core\Manage;

use Hairavel\Core\UI\Widget\Icon;
use Hairavel\Core\Util\View;

/**
 * Management terminal basic interface
 * @package Hairavel\Core\Model
 */
trait Common
{

    protected array $assign = [];

    /**
     * template assignment
     * @param $name
     * @param $value
     */
    public function assign($name, $value): void
    {
        $this->assign[$name] = $value;
    }

    /**
     * @param string $tpl
     * @return mixed
     */
    public function systemView(string $tpl = '')
    {
        return (new View($tpl, $this->assign))->render();
    }

    /**
     * @param array $node
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function systemNode(array $node = [])
    {
        return app_success('ok', [
            'node' => [
                'nodeName' => 'app-layout',
                'child' => $node
            ]
        ]);
    }

    /**
     * @param string $title
     * @param array $node
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function dialogNode(string $title = '', array $node = [])
    {
        return app_success('ok', [
            'node' => [
                'nodeName' => 'app-dialog',
                'title' => $title,
                'child' => $node
            ]
        ]);
    }

    /**
     * @param $name
     * @throws \Hairavel\Core\Exceptions\ErrorException
     */
    public function can($name)
    {
        $parsing = app_parsing();
        $route = request()->route()->getName();
        if (!auth(strtolower($parsing['layer']))->user()->can($route . '|' . $name)) {
            app_error('No permission to use this function', 403);
        }
    }

}
