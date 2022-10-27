<?php

namespace Hairavel\Core\UI\Table\Column;

/**
 * 组件接口
 * Class Component
 * @package Hairavel\Core\UI
 */
interface Component
{
    public function render($label): array;
}
