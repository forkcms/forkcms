<?php

namespace Frontend\Modules\Mailmotor;

/*
 * This file is part of the Fork CMS Mailmotor Module from SIESQO.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use \Symfony\Component\HttpKernel\KernelInterface;
use Frontend\Core\Engine\Base\Config as FrontendBaseConfig;

/**
 * This is the configuration-object
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
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
