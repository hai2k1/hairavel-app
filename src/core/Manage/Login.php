<?php

namespace Hairavel\Core\Manage;

use Illuminate\Http\Request;
use Hairavel\Core\Util\View;

/**
 * User login
 */
trait Login
{

    public function check()
    {
        $layer = strtolower(app_parsing('layer'));
        $guard = config('auth.guards.' . $layer . '.provider');
        $model = config('auth.providers.' . $guard . '.model');
        $count = $model::count();
        return app_success('Detection successful', [
            'register' => $count ? false : true
        ]);
    }

    public function submit(Request $request)
    {
        $layer = strtolower(app_parsing('layer'));
        $credentials = $request->only('username', 'password');
        if ($token = auth($layer)->attempt([$this->usernameKey ?: 'username' => $credentials['username'], 'password' => $credentials['password']])) {
            $user = auth($layer)->user();
            $username = $this->usernameKey ? $user->{$this->usernameKey} : $user->username;
            return app_success('Login successful', [
                'userInfo' => [
                    'user_id' => $user->user_id,
                    'avatar' => $user->avatar,
                    'avatar_text' => mb_substr($user->nickname ?: $username, 0, 1),
                    'rolename' => $user->roles[0]['name'],
                    'subname' => $user->nickname,
                ],
                'token' => 'Bearer' . $token,
            ]);
        }
        app_error('Account password error');
    }

    public function logout()
    {
        $layer = strtolower(app_parsing('layer'));
        auth($layer)->logout();
        return redirect(route($layer . '.login'));
    }
}
