<?php

namespace Hairavel\Core\Web;

use Hairavel\Core\Controllers\Controller;

class Area extends Controller
{

    public function index()
    {
        $level = request()->get('level', 4);
        $options = \Hairavel\Core\Model\Area::where('level', '<=', $level)->get(['name as label', 'code as value', 'code as id', 'parent_code as pid'])->toArray();
        $options = \Hairavel\Core\Util\Tree::arr2tree($options, 'id', 'pid', 'children');

        return app_success('ok', $options);
    }
}
