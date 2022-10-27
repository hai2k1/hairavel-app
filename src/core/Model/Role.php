<?php

namespace Hairavel\Core\Model;

/**
 * Class Role
 * @package Hairavel\Core\Model
 */
class Role extends \Hairavel\Core\Model\Base
{

    protected $table = 'role';

    protected $primaryKey = 'role_id';

    protected $casts = [
        'purview' => 'array',
    ];

    protected $fillable = ['guard', 'name', 'purview'];

    public static function create(array $attributes = [])
    {
        $attributes['guard'] = $attributes['guard'] ?? 'admin';
        return static::query()->create($attributes);
    }
}
