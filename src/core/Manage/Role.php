<?php

namespace Hairavel\Core\Manage;

use Hairavel\Core\Facades\Permission;
use Hairavel\Core\UI\Form;
use Hairavel\Core\UI\Table;
use Hairavel\Core\Model\Role as AuthRole;

trait Role
{
    private $config = [];

    private function parserData()
    {
        $parsing = app_parsing();

        $this->config['layer'] = $parsing['layer'];
        $this->config['guard_id'] = request()->get('global_guard_id', null);

        $route = strtolower($parsing['layer']) . '.' . strtolower($parsing['app']) . '.role';
        $model = AuthRole::class;
        return [
            'route' => $route,
            'model' => $model
        ];
    }

    /**
     * load model
     * @return void
     */
    protected function initModel(){
        $parser = $this->parserData();
        $this->model = $parser['model'];
    }

    protected function table(): Table
    {
        $parser = $this->parserData();
        $table = new Table(new $parser['model']());
        $table->model()->where('guard', $this->config['layer'])->where('guard_id', $this->config['guard_id']);
        $table->title('role management');

        $table->filter('role name', 'name', function ($query, $value) {
            $query->where('name', 'like', '%' . $value . '%');
        })->text('Please enter the character name')->quick();

        $table->action()->button('Add', $parser['route'] . '.page')->type('dialog');

        $table->column('role name', 'name');

        $column = $table->column('operation')->width(200);
        $column->link('edit', $parser['route'] . '.page', ['id' => 'role_id'])->type('dialog');
        $column->link('delete', $parser['route'] . '.del', ['id' => 'role_id'])->type('ajax', ['method' => 'post ']);
        return $table;
    }

    public function form(int $id = 0): Form
    {
        $parser = $this->parserData();
        $form = new Form(new $parser['model']());
        $form->model()->where('guard', $this->config['layer'])->where('guard_id', $this->config['guard_id']);
        $form->title('Role Information');
        $form->card(function ($form) {
            $this->formInner($form);
        });

        $form->before(function ($data, $type, $model) {
            $model->guard = strtolower($this->config['layer']);
            $model->guard_id = $this->config['guard_id'];
            $purview = explode(',', $data['purview']);
            $purview = array_filter($purview, function ($item) {
                if (stripos($item, 'desc_', 0) !== false) {
                    return false;
                }
                return true;
            });
            $model->purview = array_filter(array_values($purview));
        });

        return $form;
    }

    public function formInner($form)
    {
        $form->text('role name', 'name')->verify([
            'required',
            'min:2',
        ], [
            'required' => 'Please fill in the role name',
            'min' => 'User name cannot be less than 2 characters',
        ]);

        $form->tree('Permission selection', 'purview', function () {
            $data = Permission::getPermissions();

            $purviewData = [];
            $i = 0;
            foreach ($data as $appName => $app) {
                $i++;
                $tmp = [
                    'id' => 'desc_' . $i,
                    'name' => $app['name'],
                    'children' => []
                ];

                foreach ($app['group'] as $groupName => $item) {
                    $i++;
                    $group = [
                        'id' => 'desc_' . $i,
                        'name' => $item['name'],
                        'children' => []
                    ];
                    foreach ($item['list'] as $vo) {
                        if ($vo['auth_list']) {
                            foreach ($vo['auth_list'] as $k => $v) {
                                $group['children'][] = [
                                    'id' => $vo['value'] . '|' . $k,
                                    'name' => $v
                                ];
                            }
                        } else {
                            $group['children'][] = [
                                'id' => $vo['value'],
                                'name' => $vo['name']
                            ];
                        }
                    }
                    $tmp['children'][] = $group;
                }
                $purviewData[] = $tmp;
            }

            return $purviewData;
        })->help('Do not select all to have all permissions', true);

        return $form;
    }

}
