<?php

namespace Frontend\Modules\MailMotor;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use \Symfony\Component\HttpKernel\KernelInterface;
use Frontend\Core\Engine\Base\Config as FrontendBaseConfig;

/**
 * This is the configuration-objectJobCurrentState
 */
class Config extends FrontendBaseConfig
{
    /**
     * The default action
     *
     * @var	string
     */
    protected $defaultAction = 'Subscribe';

    /**
     * The disabled actions
     *
     * @var	array
     */
    protected $disabledActions = array();
}
