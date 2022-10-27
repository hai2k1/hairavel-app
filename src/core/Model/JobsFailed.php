<?php

namespace Hairavel\Core\Model;

/**
 * Class JobsFailed
 * @package Hairavel\Core\Model
 */
class JobsFailed extends \Hairavel\Core\Model\Base
{

    protected $table = 'jobs_failed';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $casts = [
        'payload' => 'array',
    ];

}
