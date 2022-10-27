<?php

namespace Hairavel\Core\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Hairavel\Core\Util\Tree;
use Modules\System\Service\Form;

/**
 * Trait Expend
 * @package Hairavel\Core\Model
 */
trait Expend
{
    /**
     * Model association flag
     * @var string
     */
    protected string $hasName = '';


}
