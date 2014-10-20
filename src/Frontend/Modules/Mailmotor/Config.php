<?php

namespace Frontend\Modules\Mailmotor;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */


use Frontend\Core\Engine\Base\Config as FrontendBaseConfig;

/**
 * This is the configuration-object
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class Config extends FrontendBaseConfig
{
    /**
     * The default action
     *
     * @var	string
     */
    protected $defaultAction = 'Index';

    /**
     * The disabled actions
     *
     * @var	array
     */
    protected $disabledActions = array();
}
