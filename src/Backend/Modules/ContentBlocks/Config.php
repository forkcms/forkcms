<?php

namespace ForkCMS\Backend\Modules\ContentBlocks;

use ForkCMS\Backend\Core\Engine\Base\Config as BackendBaseConfig;

/**
 * This is the configuration-object for the content_blocks module
 */
class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var string
     */
    protected $defaultAction = 'Index';
}
