<?php

namespace Hairavel\Core\Manage;

use Illuminate\Support\Facades\DB;
use Hairavel\Core\Util\View;

trait Visitor
{
    public function info()
    {
        $startTime = strtotime('-1 year');
        $hasType = request()->get('type');
        $hasId = request()->get('id');

        $data = app(\Hairavel\Core\Model\VisitorViewsData::class)
            ->select(DB::raw('SUM(pv) as pv, SUM(uv) as uv, date as label'))
            ->where('date', '>=', date('Ymd', $startTime))
            ->where('has_type', $hasType)
            ->where('has_id', $hasId)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $pvData = $data->map(function ($item) {
            $item['name'] = 'Visits';
            $item['value'] = $item['pv'];
            return $item;
        })->toArray();
        $uvData = $data->map(function ($item) {
            $item['name'] = 'Visitors';
            $item['value'] = $item['uv'];
            return $item;
        })->toArray();

        $appChart = (new \Hairavel\Core\Util\Charts)
            ->area()
            ->date(date('Y-m-d', $startTime), date('Y-m-d'), '1 days')
            ->data('Visits', $pvData)
            ->data('Visitors', $uvData)
            ->datetime(true)
            ->height(300)
            ->legend(true)
            ->render(true);


        return (new View('vendor.haibase.hairavel-app.src.core.Views.Manage.VisitorViews.info', [
            'appChart' => $appChart
        ]))->render('dialog');

        return app_success('ok', [
            'node' => [
                'nodeName' => 'app-dialog',
                'title' => 'traffic statistics',
                'child' => [
                    'nodeName' => 'div',
                    'class' => 'p-4',
                    'child' => $appChart
                ]
            ]
        ]);
    }

}
