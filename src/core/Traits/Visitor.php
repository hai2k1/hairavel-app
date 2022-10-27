<?php

namespace Hairavel\Core\Traits;

/**
 * Class Visitor
 * @package Hairavel\Core\Traits
 */
trait Visitor
{

    /**
     * Increase visitors
     * @param string $driver
     * @return bool
     * @throws \Throwable
     */
    public function viewsInc(string $driver = 'web'): bool
    {
        $id = $this->{$this->primaryKey};
        if (!$id) {
            return false;
        }
        \Hairavel\Core\Util\Visitor::increment(get_called_class(), $id, $driver);
        return true;
    }


    /**
     * delete associated content
     * @return bool
     */
    public function viewsDel(): bool
    {
        $this->views()->delete();
        $this->viewsData(0)->delete();
        return true;
    }

    /**
     * Views
     */
    public function views(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne('\Hairavel\Core\Model\VisitorViews', 'has', 'has_type');
    }

    /**
     * access data
     */
    public function viewsData($day = 7)
    {
        $data = $this->morphMany('\Hairavel\Core\Model\VisitorViewsData', 'has', 'has_type');
        if ($day) {
            $data = $data->where('date', '>=', date('Y-m-d', strtotime('-' . $day . ' day')));
        }
        return $data->orderBy('date');
    }

}
