<?php

namespace Hairavel\Core\UI;

/**
 * UI tools
 * Class Table
 * @package Hairavel\Core\UI
 */
class Tools
{

    /**
     * Parse object data
     * @param object $data
     * @param string $relation
     * @param string|null $field
     * @return string|string[]|null
     */
    public static function parsingObjData(object $data, string $relation, string $field = null)
    {
        $relationData = $data->$relation;
        if ($field) {
            if ($relationData instanceof \Illuminate\Support\Collection) {
                $tmp = [];
                foreach ($relationData as $vo) {
                    $tmp[] = self::parsingArrData($vo, $field);
                }
                $data = implode(',', $tmp);
            } else {
                $data = self::parsingArrData($relationData, $field);
            }
        } else {
            $data = '';
        }
        return $data;
    }

    /**
     * Parse array data
     * @param $data
     * @param string|null $field
     * @param bool $source
     * @return string|string[]|null
     */
    public static function parsingArrData($data, string $field = null, bool $source = false)
    {
        $field = str_replace('->', '.', $field);
        if (!$source) {
            return $field ? \Arr::get($data, $field) : '';
        }
        return \Arr::has($data, $field) ? \Arr::get($data, $field) : $field;
    }


    /**
     * Label conversion
     * @param $label
     * @param null $relation
     * @return string
     */
    public static function converLabel($label, $relation = null): string
    {
        return str_replace(['.', '->'], '_', $relation ? $relation . '_' . $label : $label);
    }

    /**
     * UI debug debugging
     * @param array|callable $node
     * @return array|mixed
     */
    public static function debug($node)
    {
        if(is_callable($node)){
            $node = $node();
        }
        if(!isset($node['node'])){
            $node = [
                'node' => $node
            ];
        }

        $node['debug'] = true;
        return $node;
    }

}
