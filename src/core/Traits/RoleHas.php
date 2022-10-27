<?php

namespace Hairavel\Core\Traits;

use Hairavel\Core\Model\Role;

/**
 * Class RoleHas
 * @package Hairavel\Core\Traits
 */
trait RoleHas
{

    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->morphToMany(Role::class, 'role', 'role_has', 'user_id', 'role_id');
    }

}
