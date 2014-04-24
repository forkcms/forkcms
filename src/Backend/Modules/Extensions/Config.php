<?php

namespace Backend\Modules\Extensions;

use Backend\Core\Engine\Base\Config as BackendBaseConfig;

/**
 * This is the configuration-object for the extensions module.
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
final class Config extends BackendBaseConfig
{
    /**
     * The default action
     *
     * @var	string
     */
    protected $defaultAction = 'Modules';

    /**
     * The disabled actions
     *
     * @var	array
     */
    protected $disabledActions = array();

    /**
     * The disabled AJAX-actions
     *
     * @var	array
     */
    protected $disabledAJAXActions = array();
}
