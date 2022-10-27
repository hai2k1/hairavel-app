<?php

namespace Hairavel\Core\Util;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

/**
 * authority management
 */
class Permission
{

    public string $guerd = 'admin';
    public $guerdId = null;
    public bool $cache = false;
    public array $routePurviews = [];
    public array $allPurviews = [];

    /**
     * Get the guardian
     * @return string
     */
    public function getGuerd(): string
    {
        return $this->guerd;
    }

    /**
     * Registration authority validator
     * @param string $guerd
     */
    public function register(string $guerd = '', $has = null): void
    {
        $this->guerd = strtolower($guerd);
        $this->guerdId = $has;
        $routes = \Route::getRoutes();
        $data = [];
        foreach ($routes as $vo) {
            if ($vo->action['auth_list']) {
                foreach ($vo->action['auth_list'] as $k => $v) {
                    $data[] = $vo->action['as'] . '|' . $k;
                }
            }
            $data[] = $vo->action['as'];
        }

        foreach ($data as $vo) {
            Gate::define($vo, fn($user) => \Hairavel\Core\Facades\Permission::checkPermissions($user, $vo));
        }
    }

    /**
     * Set user permissions
     * @param $user
     * @return bool|void
     */
    public function setPermissions($user)
    {
        if ($this->cache) {
            return true;
        }
        // get user permissions
        $roleList = $user->roles()->wherePivot('guard', $this->guerd)->wherePivot('guard_id', $this->guerdId)->get();
        // Merge multi-role permissions
        $roleList->map(function ($item) {
            foreach ($item->purview as $vo) {
                $arr = explode('|', $vo);
                $this->routePurviews[] = $arr[0];
                $this->allPurviews[] = $vo;
            }
        });
        $this->routePurviews = array_filter($this->routePurviews);
        $this->allPurviews = array_filter($this->allPurviews);
        $this->cache = true;
    }

    /**
     * Verify permissions
     * @param $user
     * @param $rule
     * @return bool
     */
    public function checkPermissions($user, $rule): bool
    {
        $this->setPermissions($user);

        if (!$this->allPurviews) {
            return true;
        }

        if (strpos($rule, '|') !== false) {
            if (in_array($rule, $this->allPurviews)) {
                return true;
            } else {
                return false;
            }
        }
        if (in_array($rule, $this->routePurviews)) {
            return true;
        }
        return false;
    }

    /**
     * Get tree permissions
     * @return array
     */
    public function getPermissions(): array
    {
        $routes = \Route::getRoutes();
        $data = [];
        foreach ($routes as $vo) {
            if ($vo->action['auth_has'] <> $this->guerd || $vo->action['public']) {
                continue;
            }
            if (!$data[$vo->action['auth_app']]) {
                $data[$vo->action['auth_app']] = [
                    'name' => $vo->action['auth_app'],
                    'group' => []
                ];
            }
            if (!$data[$vo->action['auth_app']]['group'][$vo->action['auth_group']]) {
                $data[$vo->action['auth_app']]['group'][$vo->action['auth_group']] = [
                    'name' => $vo->action['auth_group'],
                    'list' => []
                ];
            }
            $data[$vo->action['auth_app']]['group'][$vo->action['auth_group']]['list'][] = [
                'name' => $vo->action['desc'],
                'value' => $vo->action['as'],
                'auth_list' => $vo->action['auth_list']
            ];
        }
        return $data;
    }


}
