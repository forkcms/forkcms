<?php

namespace Backend\Modules\Analytics;

use Backend\Core\Engine\Base\Config as BackendBaseConfig;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the configuration-object for the analytics module
 *
 * @author Wouter Sioen <wouter@sumocoders.be>
 */
class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var string
     */
    protected $defaultAction = 'Index';

    /**
     * The disabled actions
     *
     * @var array
     */
    protected $disabledActions = array();
}
