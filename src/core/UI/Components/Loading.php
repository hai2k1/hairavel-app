<?php

namespace Hairavel\Core\UI\Components;

use Illuminate\View\Component;

/**
 * loading data
 * @package Hairavel\Core\UI\Components
 */
class Loading extends Component
{
    public $title;
    public $content;

    public function __construct($title = 'Loading data, please wait...', $content = 'If it has not been loaded for a long time, you can try to reload it')
    {
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return view('vendor.haibase.hairavel-app.src.core.UI.View.Components.loading');
    }
}
