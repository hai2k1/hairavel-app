<?php

namespace Hairavel\Core\Traits;

/**
 *Class Form
 * @package Hairavel\Core\Traits
 */
trait Form
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function form(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(\Hairavel\Core\Model\FormData::class, 'has', 'has_type');
    }

    /**
     * save the form
     * @param $formId
     * @param $data
     * @return bool
     */
    public function formSave($formId, $data): bool
    {
        $id = $this->{$this->primaryKey};
        if (!$id || !$formId) {
            return false;
        }
        return \Hairavel\Core\Util\Form::saveForm($formId, $data, $id, get_called_class());
    }

    /**
     * delete form
     * @return bool
     */
    public function formDel(): bool
    {
        $id = $this->{$this->primaryKey};
        if (!$id) {
            return false;
        }
        return $this->form()->delete();
    }

}
