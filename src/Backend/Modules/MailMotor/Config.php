<?php

namespace Backend\Modules\MailMotor;

/*
 * This file is part of the Fork CMS MailMotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use \Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Engine\Base\Config as BackendBaseConfig;

/**
 * This is the configuration-object for the MailMotor moduleJobCurrentState
 */
class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var string
     */
    protected $defaultAction = 'Settings';

    /**
     * The disabled actions
     *
     * @var array
     */
    protected $disabledActions = array();
}
