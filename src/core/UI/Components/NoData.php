<?php

namespace Hairavel\Core\UI\Components;

use Illuminate\View\Component;

/**
 * data does not exist
 * Class NoData
 * @package Hairavel\Core\UI\Components
 */
class NoData extends Component
{
    public $title;
    public $content;
    public $reload;

    public function __construct($title = 'No data found', $content = 'No data found temporarily, you can try to refresh the page', $reload = true)
    {
        $this->title = $title;
        $this->content = $content;
        $this->reload = $reload;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return view('vendor.duxphp.duxravel-app.src.core.UI.View.Components.nodata');
    }
}
