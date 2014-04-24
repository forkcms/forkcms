<?php

namespace Backend\Modules\Search;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Config as BackendBaseConfig;

/**
 * This is the configuration-object for the search module
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var	string
     */
    protected $defaultAction = 'Statistics';

    /**
     * The disabled actions
     *
     * @var	array
     */
    protected $disabledActions = array();
}
