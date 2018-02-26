<?php

namespace ForkCMS\Backend\Modules\Search;

use ForkCMS\Backend\Core\Engine\Base\Config as BackendBaseConfig;

/**
 * This is the configuration-object for the search module
 */
class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var string
     */
    protected $defaultAction = 'Statistics';
}
