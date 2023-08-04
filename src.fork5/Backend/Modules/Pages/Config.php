<?php

namespace Backend\Modules\Pages;

use Backend\Core\Engine\Base\Config as BackendBaseConfig;

/**
 * This is the configuration-object for the pages module
 */
class Config extends BackendBaseConfig
{
    /**
     * @var string
     */
    protected $defaultAction = 'PageIndex';
}
