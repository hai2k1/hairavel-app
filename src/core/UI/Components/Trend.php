<?php

namespace Hairavel\Core\UI\Components;

use Illuminate\View\Component;

/**
 * Trend icon
 *Class Trend
 * @package Hairavel\Core\UI\Components
 */
class Trend extends Component
{
    public $type;

    /**
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return view('vendor.haibase.hairavel-app.src.core.UI.View.Components.trend');
    }
}
