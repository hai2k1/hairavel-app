<?php

namespace Hairavel\Core\Model;

/**
 * Class Form
 * @package Modules\System\Model
 */
class Form extends \Hairavel\Core\Model\Base
{

    protected $table = 'form';

    protected $primaryKey = 'form_id';

    protected $casts = [
        'data' => 'array',
    ];

}
