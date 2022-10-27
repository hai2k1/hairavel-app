<?php

namespace Hairavel\Core\Model;

/**
 * Class Jobs
 * @package Hairavel\Core\Model
 */
class Jobs extends \Hairavel\Core\Model\Base
{

    protected $table = 'jobs';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $casts = [
        'payload' => 'array',
    ];

}
