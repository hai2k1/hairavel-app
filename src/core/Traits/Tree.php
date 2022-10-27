<?php

namespace Hairavel\Core\Traits;

/**
 * Class Tree
 * @package Hairavel\Core\Traits
 */
trait Tree
{
    /**
     * Get subordinates
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(get_class($this), 'parent_id');
    }

    /**
     * Get all subordinates
     * @return mixed
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Get superior
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo(get_class($this), 'parent_id');
    }

    /**
     * Get all superiors
     * @return mixed
     */
    public function allParent()
    {
        return $this->parent()->with('allParent');
    }

}
