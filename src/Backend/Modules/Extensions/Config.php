<?php

namespace ForkCMS\Backend\Modules\Extensions;

use ForkCMS\Backend\Core\Engine\Base\Config as BackendBaseConfig;

/**
 * This is the configuration-object for the extensions module.
 */
final class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var string
     */
    protected $defaultAction = 'Modules';
}
