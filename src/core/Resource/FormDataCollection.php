<?php

namespace Hairavel\Core\Resource;

use Hairavel\Core\Resource\BaseCollection;

class FormDataCollection extends BaseCollection
{

    public function toArray($request)
    {
        return $this->collection;
    }

}
