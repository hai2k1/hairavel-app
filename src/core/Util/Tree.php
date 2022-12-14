<?php

namespace Hairavel\Core\Util;

/**
 * Class Tree
 * @package Hairavel\Core\Util
 */
class Tree
{
    /**
     * Array to tree
     * @param array $list
     * @param string $id
     * @param string $pid
     * @param string $son
     * @return array
     */
    public static function arr2tree(array $list, string $id = 'id', string $pid = 'pid', string $son = 'sub'): array
    {
        [$tree, $map] = [[], []];
        foreach ($list as $item) {
            $map[$item[$id]] = $item;
        }

        foreach ($list as $item) {
            if (isset($item[$pid], $map[$item[$pid]])) {
                $map[$item[$pid]][$son][] = &$map[$item[$id]];
            } else {
                $tree[] = &$map[$item[$id]];
            }
        }
        unset($map);
        return $tree;
    }

    /**
     * Array to table tree
     * @param array $list data list
     * @param string $id ID Key
     * @param string $pid parent ID Key
     * @param string $name name Key
     * @param string $path
     * @param string $ppath
     * @return array
     */
    public static function arr2table(array $list, string $id = 'id', string $pid = 'pid', string $name = '', string $path = 'path', string $ppath = ''): array
    {
        $tree = [];
        foreach (self::arr2tree($list, $id, $pid) as $attr) {
            $attr[$path] = "{$ppath}-{$attr[$id]}";
            $attr['sub'] = $attr['sub'] ?? [];
            $attr['spt'] = substr_count($ppath, '-');
            $attr['spl'] = str_repeat(" ├ ", $attr['spt']);
            $attr['spl_' . $name] = $attr['spl'] . $attr[$name];
            $sub = $attr['sub'];
            unset($attr['sub']);
            $tree[] = $attr;
            if (!empty($sub)) {
                $tree = array_merge($tree, self::arr2table($sub, $id, $pid, $name, $path, $attr[$path]));
            }
        }
        return $tree;
    }

    /**
     * Array to path
     * @param array $data
     * @param int $parentId
     * @param string $id
     * @param string $pid
     * @param array $categories
     * @return array
     */
    public static function arr2path(array $data, int $parentId, string $id = 'id', string $pid = 'pid', array &$categories = []): array
    {
        if ($data && is_array($data)) {
            foreach ($data as $item) {
                if ($item[$id] == $parentId) {
                    $categories[] = $item;
                    self::arr2path($data, $item[$pid], $id, $pid, $categories);
                }
            }
        }
        return $categories;
    }

    /**
     * Get child id
     * @param $data
     * @param string $id
     * @return array
     */
    public static function allIds($data, string $id = 'id'): array
    {
        $arr = [];
        array_walk_recursive($data, static function ($v, $k) use (&$arr, $id) {
            if ($k == $id)
                $arr[] = $v;
        });
        return $arr;
    }
}
