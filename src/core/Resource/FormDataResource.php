<?php

namespace Hairavel\Core\Resource;

use Hairavel\Core\Resource\BaseResource;

class FormDataResource extends BaseResource
{

    public function toArray($request): array
    {
        return $this->data;
    }
}
