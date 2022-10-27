<?php

namespace Hairavel\Core\Traits;

/**
 * Class TableSortable
 * @package Hairavel\Core\Traits
 */
trait TableSortable
{

    /**
     *
     * Sort method
     * Introduce this method and register the sorting route
     */
    public function sortable()
    {
        $id = request()->input('id');
        $parent = request()->input('parent');
        $before = request()->input('before');
        $after = request()->input('after');
        $info = $this->model::find($id);

        if ($before) {
            $node = $this->model::find($before);
            $info->insertAfterNode($node);
        } else if ($after) {
            $node = $this->model::find($after);
            $info->insertBeforeNode($node);

        } else if ($parent) {
            $node = $this->model::find($parent);
            $node->prependNode($info);
        } else {
            $info->saveAsRoot();
        }
        return app_success('ok');
    }

}
