<?php

namespace Api\V1;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will initiate the API.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Init extends \Common\Core\Init
{
    /**
     * @inheritdoc
     */
    protected $allowedTypes = array('Api');

    /**
     * @param string $type The type of init to load, possible values: Backend, BackendAjax, BackendCronjob
     */
    public function initialize($type)
    {
        $type = (string) $type;

        // check if this is a valid type
        if (!in_array($type, $this->allowedTypes)) {
            exit('Invalid init-type');
        }

        // set type
        $this->type = $type;

        // set a default timezone if no one was set by PHP.ini
        if (ini_get('date.timezone') == '') {
            date_default_timezone_set('Europe/Brussels');
        }

        $this->definePaths();
        $this->setDebugging();

        \SpoonFilter::disableMagicQuotes();
        $this->initSession();
    }

    /**
     * @inheritdoc
     */
    protected function definePaths()
    {
        defined('API_CORE_PATH') || define('API_CORE_PATH', PATH_WWW . '/' . APPLICATION);
        defined('BACKEND_PATH') || define('BACKEND_PATH', PATH_WWW . '/src/Backend');
        defined('BACKEND_CACHE_PATH') || define('BACKEND_CACHE_PATH', BACKEND_PATH . '/Cache');
        defined('BACKEND_CORE_PATH') || define('BACKEND_CORE_PATH', BACKEND_PATH . '/Core');
        defined('BACKEND_MODULES_PATH') || define('BACKEND_MODULES_PATH', BACKEND_PATH . '/Modules');

        defined('FRONTEND_PATH') || define('FRONTEND_PATH', PATH_WWW . '/src/Frontend');
        defined('FRONTEND_CACHE_PATH') || define('FRONTEND_CACHE_PATH', FRONTEND_PATH . '/Cache');
        defined('FRONTEND_CORE_PATH') || define('FRONTEND_CORE_PATH', FRONTEND_PATH . '/Core');
        defined('FRONTEND_MODULES_PATH') || define('FRONTEND_MODULES_PATH', FRONTEND_PATH . '/Modules');
        defined('FRONTEND_FILES_PATH') || define('FRONTEND_FILES_PATH', FRONTEND_PATH . '/Files');
    }

    /**
     * Start session
     */
    private function initSession()
    {
        \SpoonSession::start();
    }
}
