<?php

namespace Common;

use Frontend\Core\Engine\Model;
use Frontend\Core\Engine\Exception;
use Backend\Core\Engine\Language as BL;

/**
 * Builds paths to certain items in Fork
 *
 * @author Wouter Sioen <wouter@wijs.be>
 */
class Classes
{
    public static function buildForAction($application, $module, $action)
    {
        if ($module === 'Core') {
            return '\\' . $application . '\\Core\\Actions\\' . $action;
        }

        return '\\' . $application . '\\Modules\\' . $module . '\\Actions\\' . $action;
    }

    public static function buildForAjax($application, $module, $action)
    {
        if ($module === 'Core') {
            return '\\' . $application . '\\Core\\Ajax\\' . $action;
        }

        return '\\' . $application . '\\Modules\\' . $module . '\\Ajax\\' . $action;
    }

    public static function buildForWidget($application, $module, $widget)
    {
        if ($module === 'Core') {
            return '\\' . $application . '\\Core\\Widgets\\' . $action;
        }

        return '\\' . $application . '\\Modules\\' . $module . '\\Widgets\\' . $widget;
    }
}
