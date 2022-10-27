<?php

namespace Hairavel\Core\Util;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * System cache
 */
class Cache
{

    /**
     * global file
     * @param $rule
     * @return array
     */
    public static function globList($rule): array
    {
        $list = [];
        foreach (glob($rule) as $file) {
            $list[] = $file;
        }
        return $list;
    }

    /**
     * route list
     * @param string $name
     * @return array
     */
    public static function routeList(string $name): array
    {
        $serviceList = app(Build::class)->getData('route.' . $name);
        $vendor = base_path();
        foreach ($serviceList as $key => $vo) {
            $serviceList[$key] = $vendor . '/' . $vo;
        }
        return array_filter($serviceList);
    }


}
