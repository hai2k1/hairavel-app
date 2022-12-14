<?php

namespace Hairavel\Core\Util;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * Traffic tool
 */
class Visitor
{

    /**
     * can be added
     * @param $type
     * @param $id
     * @param $driver
     */
    public static function increment($type, $id, $driver): void
    {
        $date = date('Ymd');
        DB::beginTransaction();
        try {
            $view = \Hairavel\Core\Model\VisitorViews::firstOrCreate([
                'has_type' => $type, 'has_id' => $id
            ]);
            $view->increment('pv');

            $viewData = \Hairavel\Core\Model\VisitorViewsData::firstOrCreate([
                'has_type' => $type, 'has_id' => $id, 'driver' => $driver, 'date' => $date
            ]);
            $viewData->increment('pv');
            $keys = [
                'type' => $type,
                'id' => $id,
                'driver' => $driver,
                'ip' => request()->ip(),
                'ua' => request()->userAgent(),
            ];
            $key = 'app::views::' . sha1(implode(':', $keys));
            if (!Redis::get($key)) {
                Redis::setex($key, 86400 - (time() + 8 * 3600) % 86400, 1);
                $view->increment('uv');
                $viewData->increment('uv');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

}
