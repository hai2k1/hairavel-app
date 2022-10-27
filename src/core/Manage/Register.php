<?php

namespace Hairavel\Core\Manage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Hairavel\Core\Util\View;
use Modules\System\Model\SystemUser;

/**
 * registered user
 * @package Modules\System\System
 */
trait Register
{

    public function submit(Request $request)
    {
        $parsing = app_parsing();
        $app = $parsing['app'];
        $layer = strtolower($parsing['layer']);

        Validator::make($request->input(), [
            'username' => ['required', 'string', 'max:255', 'unique:' . strtolower($app) . '_user'],
            'password' => ['required', 'string', 'min:4', 'max:20'],
        ], [
            'username.required' => 'Username input error',
            'username.unique' => 'Username cannot be repeated',
            'password.required' => 'Please enter a 4~20-digit password',
        ])->validate();


        $model = '\\Modules\\' . $app . '\\Model\\' . $app . 'User';
        $user = new $model();

        $role = \Hairavel\Core\Model\Role::firstOrCreate([
            'guard' => $layer,
        ], [
            'name' => 'Administrator',
            'purview' => []
        ]);

        $user->username = $request->input('username');
        $user->password = $request->input('password');
        $user->user_id = 1;
        $user->roles()->attach($role->role_id, ['guard' => $layer]);
        $user->save();

        return app_success('Account created successfully', [
            'userInfo' => [
                'user_id' => $user->user_id,
                'avatar' => $user->avatar,
                'avatar_text' => strtoupper(substr($user->username, 0, 1)),
                'username' => $user->username,
                'nickname' => $user->nickname,
                'rolename' => $user->roles[0]['name'],
            ],
            'token' => 'Bearer' .auth($layer)->tokenById($user->user_id),
        ]);
    }
}
