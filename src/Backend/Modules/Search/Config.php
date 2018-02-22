<?php

namespace App\Backend\Modules\Search;

use App\Backend\Core\Engine\Base\Config as BackendBaseConfig;

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
