<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Autoloader for Fork CMS.
 *
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 */
class Autoloader
{
    /**
     * @param string $className
     */
    public function load($className)
    {
        $unifiedClassName = strtolower((string) $className);
        $pathToLoad = '';

        // is it an exception?
        if (substr($unifiedClassName, 0, 5) == 'spoon') {
            // if it is a Spoon-class we can stop using this autoloader
            return;
        } elseif (substr($unifiedClassName, 0, 6) == 'common') {
            $pathToLoad = __DIR__ . '/library/base/' .
                          str_replace('common', '', $unifiedClassName) . '.php';
        }

        // file check in core
        if ($pathToLoad != '' && file_exists($pathToLoad)) {
            require_once $pathToLoad;
        }
    }
}

// register the autoloader
spl_autoload_register(array(new Autoloader(), 'load'));

// use vendor generated autoloader
require_once 'vendor/autoload.php';

// Spoon is not autoloaded via Composer but uses its own old skool autoloader
set_include_path(__DIR__ . '/vendor/spoon/library' . PATH_SEPARATOR . get_include_path());
require_once 'spoon/spoon.php';
