<?php

namespace Common\Core;

use Common\Exception\InvalidInitTypeException;
use ForkCMS\App\KernelLoader;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will initiate the application
 */
abstract class Init extends KernelLoader
{
    /**
     * Current type
     *
     * @var string
     */
    protected $type;

    /**
     * Allowed types
     *
     * @var array
     */
    protected $allowedTypes;

    /**
     * @param string $type The type of init to load, possible values are: frontend, frontend_ajax, frontend_js.
     */
    public function initialize(string $type): void
    {
        $type = (string) $type;

        // check if this is a valid type
        if (!in_array($type, $this->allowedTypes)) {
            throw new InvalidInitTypeException($type, $this->allowedTypes);
        }
        $this->type = $type;

        // set a default timezone if no one was set by PHP.ini
        if (ini_get('date.timezone') === '') {
            date_default_timezone_set('Europe/Brussels');
        }

        // get last modified time for globals
        $lastModifiedTime = @filemtime(PATH_WWW . '/app/config/parameters.yml');

        // reset last modified time if needed when invalid or debug is active
        if ($lastModifiedTime === false || $this->getContainer()->getParameter('kernel.debug')) {
            $lastModifiedTime = time();
        }

        // define as a constant
        defined('LAST_MODIFIED_TIME') || define('LAST_MODIFIED_TIME', $lastModifiedTime);
    }
}
