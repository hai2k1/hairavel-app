<?php

namespace Hairavel\Core\Resource;

use Hairavel\Core\Resource\BaseResource;

class FormResource extends BaseResource
{

    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'audit' => $this->audit,
        ];
    }
}
