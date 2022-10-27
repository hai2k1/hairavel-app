<?php

namespace Hairavel\Core\Manage;

use Hairavel\Core\UI\Table;
use Hairavel\Core\UI\Widget;

trait Operate
{


    protected function table(): Table
    {

        $parser = app_parsing();
        $layer = strtolower($parser['layer']);
        $route = strtolower($parser['layer']) . '.' . strtolower($parser['app']);
        $table = new \Hairavel\Core\UI\Table(new \Hairavel\Core\Model\VisitorOperate());
        $table->title('Operation log');
        $table->model()->where('has_type', $layer);
        $table->model()->orderBy('updated_at', 'desc');

        $table->map([
            'method',
            'created_at' => function($item) {
                return $item->created_at->format('Y-m-d H:i:s');
            },
            'updated_at' => function($item) {
                return $item->updated_at->format('Y-m-d H:i:s');
            }
        ]);

        $table->filter('user', 'user_id', function ($query, $value) {
            $query->where('has_id', $value);
        })->select([], function ($object) use ($route) {
            $object->search(route($route . '.user.data'));
        })->quick();

        $table->filter('start date', 'start', function ($query, $value) {
            $query->where('created_at', '>=', $value);
        })->date();
        $table->filter('end date', 'stop', function ($query, $value) {
            $query->where('updated_at', '<=', $value);
        })->date();

        $table->column('user', 'username');
        $table->column('page', 'desc')->desc('name');
        $table->column('client', 'ip')->desc('ua', function ($value, $item) {
            $html = [];
            if ($item->mobile) {
                $html[] = Widget::Icon('fa fa-phone');
            } else {
                if ($item->device === 'OS X') {
                    $html[] = Widget::Icon('fab fa-apple');
                } elseif ($item->device === 'Windows') {
                    $html[] = Widget::Icon('fab fa-windows');
                } else {
                    $html[] = Widget::Icon('fab fa-linux');
                }
            }
            return implode(' ', $html) . ' ' . $item->device . ' - ' . $item->browser;
        });
        $table->column('operation time', 'updated_at')->desc('time', function ($value) {
            return $value .'s';
        });

        $column = $table->column('details')->width(150);
        $column->link('View data', $route . '.operate.info', ['id' => 'uuid'])->type('drawer');

        return $table;
    }

    public function info($id)
    {
        $info = \Hairavel\Core\Model\VisitorOperate::find($id);


        $data = [];
        $data[] = [
            'label' => 'user',
            'value' => $info->username,
        ];
        $data[] = [
            'label' => 'way',
            'value' => $info->method,
        ];
        $data[] = [
            'label' => 'route',
            'value' => $info->route,
        ];
        $data[] = [
            'label' => 'description',
            'value' => $info->desc,
        ];
        $data[] = [
            'label' => 'IP',
            'value' => $info->ip,
        ];
        $data[] = [
            'label' => 'browser',
            'value' => $info->browser,
        ];
        $data[] = [
            'label' => 'system',
            'value' => $info->device,
        ];
        $data[] = [
            'label' => 'response',
            'value' => $info->time . 's',
        ];
        $data[] = [
            'label' => 'request time',
            'value' => $info->created_at->format('Y-m-d H:i:s'),
        ];
        $data[] = [
            'label' => 'end time',
            'value' => $info->updated_at->format('Y-m-d H:i:s'),
        ];

        return $this->dialogNode('operation details', [
            'nodeName' => 'div',
            'class' => 'p-4',
            'child' => [
                [
                    'nodeName' => 'div',
                    'child' => 'user information',
                    'class' => 'pb-4 text-base',
                ],
                [
                    'nodeName' => 'a-descriptions',
                    'column' => 1,
                    'bordered' => true,
                    'data' => $data
                ],
                $info->params ? [
                    'nodeName' => 'div',
                    'child' => 'request data',
                    'class' => 'py-4 text-base',
                ] : [],
                $info->params ? [
                    'nodeName' => 'pre',
                    'class' => 'bg-gray-100 block p-4',
                    'child' => json_encode($info->params, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)
                ] : [],
            ]
        ]);
    }

}
