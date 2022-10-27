<?php

namespace Hairavel\Core\Manage;

use Hairavel\Core\Events\ManageTable;
use Hairavel\Core\Events\ManageForm;
use Hairavel\Core\Events\ManageStatus;
use Hairavel\Core\Events\ManageClear;
use Hairavel\Core\Events\ManageRecovery;
use Hairavel\Core\Events\ManageExport;
use Hairavel\Core\Events\ManageDel;
use Hairavel\Core\UI\Event;
use Hairavel\Core\UI\Form;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Management port extension function interface
 * Trait Expend
 * @package Hairavel\Core\Controller
 * @method \Hairavel\Core\UI\Table table()
 * @method \Hairavel\Core\UI\Form form($id = 0)
 * @method saveEvent($table, Form $form, $class, $type)
 * @method storeData($data, $id)
 * @method delData($id = 0)
 * @method clearData($id, $info)
 * @method dataSearch()
 * @method dataWhere($query)
 * @method dataField()
 * @method dataManageUrl($item)
 * @method dataInfoUrl($item)
 * @method exportData($export)
 */
trait Expend
{

    public string $model;
    public ?string $indexUrl = null;

    /**
     * event name
     * @return false|string
     */
    protected function eventName()
    {
        return get_called_class();
    }

    /**
     * table refresh event
     * @return array
     */
    protected function tableReload()
    {
        $eventName = md5($this->eventName());
        return (new \Hairavel\Core\UI\Script())->add("dux.event.event.emit('table-action-{$eventName}', 'reload')")->render();
    }

    /**
     * table is transferred to the specified page
     * @param $page
     * @return array
     */
    protected function tableToPage($page = 1)
    {
        $eventName = md5($this->eventName());
        return (new \Hairavel\Core\UI\Script())->add("dux.event.event.emit('table-action-{$eventName}', 'to-page',{$page})" )->render();
    }

    public function index()
    {
        $table = $this->table();
        event(new ManageTable($this->eventName(), $table));
        return $table->render();
    }

    public function ajax()
    {
        $table = $this->table();
        event(new ManageTable($this->eventName(), $table));
        return $table->renderAjax();
    }

    public function page($id = 0)
    {
        if ($id) {
            $this->can('edit');
        } else {
            $this->can('add');
        }
        $form = $this->form($id);
        if ($id && $form->modelElo()) {
            $form->setKey($form->modelElo()->getKeyName(), $id);
        }
        event(new ManageForm($this->eventName(), $form));
        return $form->render();
    }

    public function save($id = 0)
    {
        if ($id) {
            $this->can('edit');
        } else {
            $this->can('add');
        }
        $form = $this->form($id);
        if ($id && $form->modelElo) {
            $form->setKey($form->modelElo()->getKeyName(), $id);
        }
        event(new ManageForm($this->eventName(), $form));
        $data = $form->save();
        if ($data instanceof Collection && method_exists($this, 'storeData')) {
            $data = $this->storeData($data, $id);
        }

        $data = [];
        if (method_exists($this, 'table')) {
            if (method_exists($this, 'saveEvent')) {
                $data = $this->saveEvent($this->table(), $form, $this->eventName(), $id ? 'edit' : 'add');
            } else {
                $data = $form->callbackEvent($this->table(), $this->eventName(), $id ? 'edit' : 'add');
            }
        }

        if ($this->indexUrl === null) {
            if ($form->getDialog()) {
                $action = $data ? '' : "routerPush:";
            } else {
                $action = '/' .\Str::beforeLast(request()->path(), '/save');
            }
        } else {
            $action = $this->indexUrl;
        }

        return app_success('Save the record successfully', $data, $action);
    }

    public function del($id = 0)
    {
        if (!$id) {
            $id = request()->input('id');
        }
        if (!$id) {
            app_error('Delete parameter error');
        }

        DB::beginTransaction();
        $status = false;
        if (method_exists($this, 'delData')) {
            $status = $this->delData($id);
            if (!$status) {
                DB::rollBack();
                app_error('Failed to delete record');
            }
        }

        event(new ManageDel($this->eventName(), $id));
        if ($this->model) {
            $info = $this->model::find($id);
            if(empty($info)){
                app_error('Invalid data');
            }
            $status = $info->delete();
        }
        if (!$status) {
            DB::rollBack();
            app_error('Failed to delete record');
        }
        DB::commit();

        $data = [];
        if (method_exists($this, 'delEvent')) {
            $data = $this->delEvent($this->model ? $info : $id, $this->eventName());
        } else {
            $data = (new Event($this->eventName()))->add('del', $id)->render();
        }

        return app_success('Delete records successfully', $data);
    }

    public function batchDel($ids = 0){
        if (!$ids) {
            $ids = request()->input('ids');
        }
        if (!$ids) {
            app_error('Delete parameter error');
        }
        if(!is_array($ids)){
            $ids = explode(',',$ids);
        }
        DB::beginTransaction();
        $status = false;
        if (method_exists($this, 'delData')) {
            foreach ($ids as $id){
                $status = $this->delData($id);
                if (!$status) {
                    DB::rollBack();
                    app_error('Failed to delete record');
                }
            }
        }
        if ($this->model) {
            $status = $this->model::destroy($ids);
        }
        if (!$status) {
            DB::rollBack();
            app_error('Failed to delete record');
        }
        DB::commit();

        return app_success('Delete records successfully', $this->tableReload());
    }

    public function export()
    {
        $table = $this->table();
        event(new ManageExport($this->eventName(), $table));
        if (!method_exists($this, 'exportData')) {
            app_error('', 404);
        }
        $table->export(function ($export) {
            return $this->exportData($export);
        });
    }

    public function recovery($id = 0)
    {
        if (!$id) {
            $id = request()->input('id');
        }
        if (!$id) {
            app_error('parameter error');
        }
        event(new ManageRecovery($this->eventName(), $id));
        if ($this->model) {
            $this->model::withTrashed()->find($id)->restore();
        }
        return app_success('recovery record successful');
    }

    public function clear($id = 0)
    {
        if (!$id) {
            $id = request()->input('id');
        }
        if (!$id) {
            app_error('parameter error');
        }
        DB::beginTransaction();
        $info = $this->model::withTrashed()->find($id);
        if (method_exists($this, 'clearData')) {
            $status = $this->clearData($id, $info);
            if (!$status) {
                DB::rollBack();
                app_error('Failed to delete record');
            }
        }
        event(new ManageClear($this->eventName(), $id));
        if ($this->model) {
            $info->forceDelete();
        }
        DB::commit();
        return app_success('Delete the record successfully');
    }

    public function status($id = 0)
    {
        if (!$id) {
            $id = request()->input('id');
        }
        if (!$id) {
            app_error('parameter error');
        }
        $field = request()->input('field', 'status');
        $value = request()->input($field);
        if (!$id || !$field) {
            app_error('Status parameter passed error');
        }
        event(new ManageStatus($this->eventName(), $id));
        DB::beginTransaction();
        try{
            if (method_exists($this, 'statusData')) {
                $status = $this->statusData($id,$field,$value);
                if (!$status) {
                    app_error('Change status failed');
                }
            }
            $model = $this->model::find($id);
            $model->{$field} = $value;
            $model->save();

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }

        $form = new Form($model);
        $form->info = $model;
        $form->modelId = $model->getKey();
        $data = [];
        if (method_exists($this, 'table')) {
            if (method_exists($this, 'saveEvent')) {
                $data = $this->saveEvent($this->table(), $form, $this->eventName(), 'edit');
            } else {
                $data = $form->callbackEvent($this->table(), $this->eventName(), 'edit');
            }
        }

        $action = $data ? '' : "routerPush:";

        return app_success('Change status succeeded', $data,$action);
    }

    public function data()
    {
        $name = request()->get('query');
        $limit = request()->get('limit', 0);
        $id = request()->get('id');
        $data = new $this->model();
        $key = $data->getKeyName();
        if ($name) {
            $nameKey = [];
            if (method_exists($this, 'dataSearch')) {
                $nameKey = $this->dataSearch();
            }
            $data = $data->where(function ($query) use ($nameKey, $name) {
                foreach ($nameKey as $vo) {
                    $query->orWhere($vo, 'like', "%{$name}%");
                }
            });

        }
        if ($id) {
            $ids = !is_array($id) ? explode(',', $id) : $id;
            $ids = array_filter($ids);
            if ($ids) {
                $ids = implode(',', $ids);
                $data = $data->orderByRaw(DB::raw("FIELD($key, $ids) desc"));
            }
        }

        if (method_exists($this, 'dataOrder')) {
            $data = $this->dataOrder($data);
        }else{
            $data->orderBy($key);
        }

        if (method_exists($this, 'dataWhere')) {
            $data = $this->dataWhere($data);
        }

        $field = ['name'];
        if (method_exists($this, 'dataField')) {
            $field = $this->dataField();
        }
        $field[] = $key . ' as id';
        $retData = [];
        if($limit){
            $paginate = $data->paginate($limit, $field);
            $data = $paginate->getCollection();

            $retData['total'] = $paginate->total();
            $retData['pageSize'] = $paginate->perPage();
            $retData['totalPage'] = $paginate->lastPage();
        }else{
            $data = $data->get($field);
        }

        if (method_exists($this, 'dataCallback')) {
            $data = $this->dataCallback($data);
        }

        $data = $data->toArray();

        $manageUrl = false;
        if (method_exists($this, 'dataManageUrl')) {
            $manageUrl = true;
        }
        $infoUrl = false;
        if (method_exists($this, 'dataInfoUrl')) {
            $infoUrl = true;
        }
        foreach ($data as &$item) {
            if ($manageUrl) {
                $item['manage_url'] = $this->dataManageUrl($item);
            }
            if ($infoUrl) {
                $item['info_url'] = $this->dataInfoUrl($item);
            }
        }

        $retData['data'] = $data;
        return app_success('ok', $retData);
    }

}
