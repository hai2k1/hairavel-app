<?php

namespace Hairavel\Core\Api;

use Hairavel\Core\Api\Api;
use Modules\Article\Resource\TagsCollection;
use Hairavel\Core\Resource\FormDataCollection;
use Hairavel\Core\Resource\FormDataResource;
use Hairavel\Core\Resource\FormResource;

class Form extends Api
{

    public function list($id)
    {
        $formInfo = \Hairavel\Core\Service\Form::form($id);
        $data = new \Hairavel\Core\Model\FormData();
        $data = $data->where('status', 1)->where('form_id', $id);
        $res = new FormDataCollection($data->paginate());
        return $this->success($res);
    }

    public function info($id)
    {
        [$info, $formInfo] = \Hairavel\Core\Service\Form::info($id);
        return $this->success(new FormDataResource($info));
    }

    public function push($id)
    {
        $key = request('key');
        if (!$key) {
            return $this->error('Missing captcha parameter');
        }
        $formInfo = \Hairavel\Core\Service\Form::push($id, $key);
        return $this->success(new FormResource($formInfo));
    }

}
