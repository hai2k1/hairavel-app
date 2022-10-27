<?php

namespace Hairavel\Core\Model;

/**
 *Class API
 * @package Hairavel\Core\Model
 */
class Api extends \Hairavel\Core\Model\Base
{

    protected $table = 'api';

    protected $primaryKey = 'api_id';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * Get platform type
     * @return array
     */
    public static function getPlatformType()
    {
        return [
            'h5' => 'H5',
            'Wechat' => 'WeChat Official Account',
            'weapp' => 'WeChat applet',
            'app' => 'APP',
            'web' => 'computer'
        ];
    }

}
