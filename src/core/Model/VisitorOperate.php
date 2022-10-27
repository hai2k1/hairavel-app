<?php

namespace Hairavel\Core\Model;

use Ramsey\Uuid\Uuid;

/**
 * Class VisitorOperate
 * @package Hairavel\Core\Model
 */
class VisitorOperate extends \Hairavel\Core\Model\Base
{

    protected $table = 'visitor_operate';

    protected $primaryKey = 'uuid';

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        });
    }

    protected $casts = [
        'uuid' => 'string',
        'params' => 'array',
    ];

}
