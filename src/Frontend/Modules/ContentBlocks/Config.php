<?php

namespace Frontend\Modules\ContentBlocks;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Engine\Base\Config as BaseConfig;

/**
 * This is the configuration-object
 */
class Config extends BaseConfig
{
    /**
     * The default action
     *
     * @var string
     */
    protected $defaultAction = 'Detail';
}
