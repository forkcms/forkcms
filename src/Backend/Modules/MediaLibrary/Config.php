<?php

namespace ForkCMS\Backend\Modules\MediaLibrary;

use ForkCMS\Backend\Core\Engine\Base\Config as BackendBaseConfig;

/**
 * This is the configuration-object for the media library module
 */
class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var string
     */
    protected $defaultAction = 'MediaItemIndex';
}
