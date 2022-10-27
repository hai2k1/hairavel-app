<?php

namespace Hairavel\Core\Manage;

use Illuminate\Validation\Rule;

trait User
{

    private function parserData()
    {
        $parsing = app_parsing();
        $route = strtolower($parsing['layer']) . '.' . strtolower($parsing['app']) . '.user';
        $app = $parsing['app'];
        $model = '\\Modules\\' . $app . '\\Model\\' . $app . 'User';
        return [
            'route' => $route,
            'model' => $model,
            'layer' => strtolower($parsing['layer'])
        ];
    }

    protected function table()
    {
        $parser = $this->parserData();
        $table = new \Hairavel\Core\UI\Table(new $parser['model']());
        $table->title('User Management');
        $table->action()->button('Add', $parser['route'] . '.page')->type('dialog');

        $table->filter('username', 'username', function ($query, $value) {
            $query->where('username', 'like', '%' . $value . '%');
        })->text('Please enter the user name to search')->quick();

        $table->filter('role', 'role_id', function ($query, $value) {
            $query->whereHas('roles', function ($query) use ($value) {
                $query->where((new $this->model)->roles()->getTable() . '.role_id', $value);
            });
        })->select(function () {
            return \Hairavel\Core\Model\Role::where('guard', 'admin')->pluck('name', 'role_id')->toArray();
        })->quick();

        $table->column('#', 'user_id')->width(80);
        $table->column('username', 'username');
        $table->column('nickname', 'nickname');
        $table->column('roles', 'roles.name');
        $table->column('status', 'status')->status([
            1 => 'normal',
            0 => 'disabled'
        ], [
            1 => 'blue',
            0 => 'red'
        ]);

        $column = $table->column('operation')->width(200);
        $column->link('edit', $parser['route'] . '.page', ['id' => 'user_id'])->type('dialog');
        $column->link('delete', $parser['route'] . '.del', ['id' => 'user_id'])->type('ajax', ['method' => 'post ']);

        return $table;
    }

    public function form(int $id = 0)
    {
        $parser = $this->parserData();
        $form = new \Hairavel\Core\UI\Form(new $parser['model']());
        $form->dialog(true);
        $form->setKey('user_id', $id);

        $form->select('role', 'role_ids', function () use ($parser) {
            return \Hairavel\Core\Model\Role::where('guard', $parser['layer'])->pluck('name', 'role_id');
        }, 'roles')->multi()->verify([
            'required',
        ], [
            'required' => 'Please select a role',
        ])->sort(-1)->pivot([
            'guard' => $parser['layer']
        ]);

        $form->text('username', 'username')->verify([
            'required',
            'min:4',
            Rule::unique((new $parser['model'])->getTable())->ignore($id, 'user_id'),
        ], [
            'required' => 'Please fill in the user name',
            'unique' => 'User name cannot be repeated',
            'min' => 'User name cannot be less than 4 digits',
        ]);

        $form->text('nickname', 'nickname')->verify('required', [
            'required' => 'Please fill in the nickname',
        ]);

        $form->password('password', 'password')->verify('required|min:4', [
            'required' => 'Please fill in the password',
            'min' => 'The password cannot be less than 4 digits',
        ], 'add')->verify('nullable|min:4', [
            'min' => 'The password cannot be less than 4 digits',
        ], 'edit')->value('')->help($id ? 'Please leave blank if you dont want to change the password' : '');

        $form->radio('status', 'status', [
            1 => 'enable',
            0 => 'disable',
        ]);

        return $form;
    }

    public function dataSearch()
    {
        return ['nickname', 'username'];
    }

    public function dataField()
    {
        return ['username as name'];
    }

}
