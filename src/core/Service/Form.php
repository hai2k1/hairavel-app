<?php

namespace Hairavel\Core\Service;

/**
 * Form related
 */
class Form
{

    public static function form($id)
    {
        $formInfo = \Hairavel\Core\Model\Form::find($id);
        if (!$formInfo || $formInfo->manage) {
            app_error('Form does not exist', 404);
        }
        return $formInfo;
    }

    public static function info($id)
    {
        $info = \Hairavel\Core\Model\FormData::find($id);
        if (!$info) {
            app_error('Information does not exist', 404);
        }
        $formInfo = \Hairavel\Core\Model\Form::find($info->form_id);
        if (!$formInfo || $formInfo->manage) {
            app_error('Form does not exist', 404);
        }
        return [$info, $formInfo];
    }

    public static function push($id, $captchaKey = '')
    {
        $formInfo = \Hairavel\Core\Model\Form::find($id);
        if (!$formInfo || $formInfo->manage || !$formInfo->submit) {
            app_error('Form does not exist', 404);
        }
        $rules = [
            'captcha' => $captchaKey ? 'required|captcha_api:'. $captchaKey . ',math' : 'required|captcha'
        ];
        $validator = validator()->make(request()->input(), $rules);
        if ($validator->fails()) {
            app_error('The verification code is entered incorrectly');
        }

        $lastInfo = \Hairavel\Core\Model\FormData::latest()->first();

        if ($lastInfo->created_at->lt($formInfo['Interval'])) {
            app_error('Submission is too fast, please wait a moment');
        }

        $input = request()->input();
        $formData = $formInfo->data;
        $uploadFields = [];
        foreach ($formData as $vo) {
            if ($vo['type'] === 'image' || $vo['type'] === 'file' || $vo['type'] === 'images') {
                $uploadFields[] = $vo['field'];
            }
        }
        if ($uploadFields) {
            $files = request()->allFiles();
            $filetKeys = array_keys($files);
            foreach ($filetKeys as $key) {
                if (!in_array($key, $uploadFields)) {
                    app_error('Illegal file upload');
                }
            }
            $files = \Hairavel\Core\Util\Upload::load('web');
            $fileData = [];
            foreach ($files as $file) {
                $fileData[$file['field']][] = $file;
            }
            foreach ($formData as $vo) {
                if ($vo['type'] === 'image' || $vo['type'] === 'file') {
                    $input[$vo['field']] = $fileData[$vo['field']][0]['url'];
                }
                if ($vo['type'] === 'images') {
                    $input[$vo['field']] = array_column($fileData[$vo['field']], 'url');
                }
            }
        }

        \Hairavel\Core\Util\Form::saveForm($id, $input);
        return $formInfo;
    }
}
