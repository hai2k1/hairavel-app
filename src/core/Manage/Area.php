<?php

namespace Hairavel\Core\Manage;

use Hairavel\Core\Util\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait Area
{

    public string $model = \Hairavel\Core\Manage\Model\ToolsArea::class;

    protected function table()
    {
        $table = new \Hairavel\Core\UI\Table(new $this->model());
        $table->title('regional data');

        $table->action()->button('import', 'admin.system.area.import')->type('dialog');

        $table->filter('name', 'name', function ($query, $value) {
            $query->where('name', 'like', '%' . $value . '%');
        })->text('Please enter the area name to search')->quick();

        $table->column('area code', 'code');
        $table->column('name', 'name');

        $column = $table->column('operation')->width(80);
        $column->link('delete', 'admin.system.area.del', ['id' => 'area_id'])->type('ajax', ['method' => 'post']) ;

        return $table;
    }

    public function import()
    {
        $form = new \Hairavel\Core\UI\Form(collect());
        $form->action(route('admin.system.area.importData'));
        $form->dialog(true);
        $form->file('import data', 'file')->verify([
            'required',
        ], [
            'required' => 'Please select upload data',
        ])->help('Data source: <a href="http://lbsyun.baidu.com/index.php?title=open/dev-res" target="_blank">[Baidu map administrative division adcode mapping Table]</a>, the existing data will be overwritten after uploading', true);
        return $form->render();
    }

    public function importData()
    {
        $file = request()->input('file');
        $data = Excel::import($file);
        $data = array_slice($data, 1);
        $newData = [];
        foreach ($data as $key => $vo) {

            if (!$newData[$vo[1]]) {
                $newData[$vo[1]] = [
                    'parent_code' => 0,
                    'code' => $vo[1],
                    'name' => $vo[2],
                    'level' => 1,
                ];
            }

            if (!$newData[$vo[3]]) {
                $newData[$vo[3]] = [
                    'parent_code' => $vo[1],
                    'code' => $vo[3],
                    'name' => $vo[4],
                    'level' => 2,
                ];
            }

            if (!$newData[$vo[5]]) {
                $newData[$vo[5]] = [
                    'parent_code' => $vo[3],
                    'code' => $vo[5],
                    'name' => $vo[6],
                    'level' => 3,
                ];
            }

            if (!$newData[$vo[7]]) {
                $newData[$vo[7]] = [
                    'parent_code' => $vo[5],
                    'code' => $vo[7],
                    'name' => $vo[8],
                    'level' => 4,
                ];
            }
        }

        $list = array_chunk(collect($newData)->sortBy('code')->toArray(), 1000);

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('system_area')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');


        foreach ($list as $vo) {
            DB::table('system_area')->insert(array_values($vo));
        }

        return app_success('Import data successfully', [], route('admin.system.area'));

    }

}
