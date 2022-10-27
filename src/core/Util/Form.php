<?php

namespace Hairavel\Core\Util;

use Hairavel\Core\Model\FormData;

/**
 * Form service
 */
class Form
{

    /**
     * Get form UI
     * @param $formId
     * @param \Hairavel\Core\UI\Form $form
     * @param int $id
     * @param string $hasType
     * @return \Hairavel\Core\UI\Form
     */
    public static function getFormUI($formId, \Hairavel\Core\UI\Form $form, int $id = 0, string $hasType = ''): \Hairavel\Core\UI\Form
    {
        $model = new \Hairavel\Core\Model\FormData();
        $info = [];
        if ($id) {
            if ($hasType) {
                $info = $model->where('has_id', $id)->where('has_type', $hasType)->first();
            } else {
                $info = $model->where('data_id', $id)->first();
            }
            $info = $info->data;
        }
        $formInfo = \Hairavel\Core\Model\Form::find($formId);
        $formData = $formInfo->data;
        $formUI = self::formUI();
        foreach ($formData as $key => $vo) {
            if ($formUI[$vo['type']]) {
                call_user_func($formUI[$vo['type']]['ui'], $vo, $form, $info[$vo['field']]);
            }
        }
        return $form;
    }

    /**
     * save the form
     * @param int $formId
     * @param array|object $data
     * @param int $id association id | data id
     * @param string $hasType form type
     * @return bool
     */
    public static function saveForm(int $formId, $data, int $id = 0, string $hasType = ''): bool
    {
        $formInfo = \Hairavel\Core\Model\Form::find($formId);
        $formData = $formInfo->data;

        $formUI = self::formUI();
        $tmpArr = [];
        foreach ($formData as $vo) {
            $tmpArr[$vo['field']] = $data[$vo['field']];
            if ($formUI[$vo['type']]['verify']) {
                call_user_func($formUI[$vo['type']]['verify'], $vo, $data[$vo['field']]);
            }
        }

        $model = new \Hairavel\Core\Model\FormData();
        if ($id) {
            if ($hasType) {
                $info = $model->where('form_id', $formId)->where('has_id', $id)->where('has_type', $hasType)->first();
            } else {
                $info = $model->where('form_id', $formId)->where('data_id', $id)->first();
            }
            if ($info) {
                $model = $info;
            }
        }

        if ($formInfo['manage'] && $formInfo['submit'] && $formInfo['audit']) {
            $model->status = 0;
        } else {
            $model->status = 1;
        }

        if ($hasType) {
            $model->has_type = $hasType;
            $model->has_id = $id;
        } else {
            $model->has_type = FormData::class;
        }
        $model->data = $tmpArr;
        $model->form_id = $formId;
        $model->save();
        return true;
    }

    /**
     * delete association
     * @param $id
     * @param string $hasType
     * @return bool
     * @throws \Exception
     */
    public static function delForm($id, string $hasType = ''): bool
    {
        $model = new \Hairavel\Core\Model\FormData();
        if ($hasType) {
            $model->where('has_id', $id)->where('has_type', $hasType)->delete();
        } else {
            $model->where('data_id', $id)->delete();
        }
        return true;
    }


    /**
     * Set UI
     * @return array
     */
    public static function formUI(): array
    {
        return [
            'text' => [
                'ui' => function ($config, \Hairavel\Core\UI\Form $form, $value) {
                    switch ($config['data']['type']) {
                        case 'number':
                            $el = $form->number($config['name'], $config['field']);
                            break;
                        case 'textarea':
                            $el = $form->textarea($config['name'], $config['field']);
                            break;
                        case 'password':
                            $el = $form->password($config['name'], $config['field']);
                            break;
                        case 'text':
                        default:
                            $el = $form->text($config['name'], $config['field']);
                    }
                    $el->value($value);
                },
                'verify' => function ($config, $value) {
                    if ($config['data']['required']) {
                        if (!$value) {
                            app_error('Please fill in'. $config['name']);
                        }
                    }
                }
            ],
            'select' => [
                'ui' => function ($config, \Hairavel\Core\UI\Form $form, $value) {
                    $form->select($config['name'], $config['field'], function () use ($config) {
                        $option = array_filter($config['data']['options']);
                        $tmpArr = [];
                        foreach ($option as $vo) {
                            $tmpArr[$vo] = $vo;
                        }
                        return $tmpArr;
                    })->value($value);
                }
            ],
            'radio' => [
                'ui' => function ($config, \Hairavel\Core\UI\Form $form, $value) {
                    $form->radio($config['name'], $config['field'], function () use ($config) {
                        $option = array_filter($config['data']['options']);
                        $tmpArr = [];
                        foreach ($option as $vo) {
                            $tmpArr[$vo] = $vo;
                        }
                        return $tmpArr;
                    })->value($value);
                }
            ],
            'checkbox' => [
                'ui' => function ($config, \Hairavel\Core\UI\Form $form, $value) {
                    $form->checkbox($config['name'], $config['field'], function () use ($config) {
                        $option = array_filter($config['data']['options']);
                        $tmpArr = [];
                        foreach ($option as $vo) {
                            $tmpArr[$vo] = $vo;
                        }
                        return $tmpArr;
                    })->value($value);
                }
            ],
            'image' => [
                'ui' => function ($config, \Hairavel\Core\UI\Form $form, $value) {
                    $form->image($config['name'], $config['field'])->type($config['data']['type'] ? 'upload' : 'manage')->value ($value);
                },
                'verify' => function ($config, $value) {
                    if ($config['data']['required']) {
                        if (!$value) {
                            app_error('Please upload'. $config['name']);
                        }
                    }
                }
            ],
            'images' => [
                'ui' => function ($config, \Hairavel\Core\UI\Form $form, $value) {
                    $form->images($config['name'], $config['field'])->type($config['data']['type'] ? 'upload' : 'manage')->value ($value);
                },
                'verify' => function ($config, $value) {
                    if ($config['data']['required']) {
                        if (!$value) {
                            app_error('Please fill in'. $config['name']);
                        }
                    }
                    if ($config['data']['num']) {
                        if (count($value) > $config['data']['num']) {
                            app_error('upload' . $config['name'] . 'exceeded' . $config['data']['num'] . 'sheet');
                        }
                    }
                }
            ],
            'file' => [
                'ui' => function ($config, \Hairavel\Core\UI\Form $form, $value) {
                    $form->file($config['name'], $config['field'])->attr('data-mode', $config['data']['type'] ? 'upload' : ' manage')->value($value);
                },
                'verify' => function ($config, $value) {
                    if ($config['data']['required']) {
                        if (!$value) {
                            app_error('Please upload'. $config['name']);
                        }
                    }
                }
            ],
            'date' => [
                'ui' => function ($config, \Hairavel\Core\UI\Form $form, $value) {
                    if ($config['data']['type'] === 'date') {
                        $form->date($config['name'], $config['field'])->value($value);
                    }
                    if ($config['data']['type'] === 'time') {
                        $form->time($config['name'], $config['field'])->value($value);
                    }
                    if ($config['data']['type'] === 'datetime') {
                        $form->datetime($config['name'], $config['field'])->value($value);
                    }
                    if ($config['data']['type'] === 'range') {
                        $form->daterange($config['name'], $config['field'])->value($value);
                    }
                },
                'verify' => function ($config, $value) {
                    if ($config['data']['required']) {
                        if (!$value) {
                            app_error('Please select' . $config['name']);
                        }
                    }
                }
            ],
            'editor' => [
                'ui' => function ($config, \Hairavel\Core\UI\Form $form, $value) {
                    $form->editor($config['name'], $config['field'])->value($value);
                },
                'verify' => function ($config, $value) {
                    if ($config['data']['required']) {
                        if (!$value) {
                            app_error('Please enter' . $config['name']);
                        }
                    }
                }
            ],
            'color' => [
                'ui' => function ($config, \Hairavel\Core\UI\Form $form, $value) {
                    if ($config['data']['type'] === 'color') {
                        $form->color($config['name'], $config['field'])->value($value);
                    }
                    if ($config['data']['type'] === 'picker') {
                        $form->color($config['name'], $config['field'])->value($value)->picker();
                    }
                }
            ],
        ];
    }
}
