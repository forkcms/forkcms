<?php

namespace Frontend;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will initiate the frontend-application
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Matthias Mullie <forkcms@mullie.eu>
 */
class Init extends \Common\Core\Init
{
    /**
     * @inheritdoc
     */
    protected $allowedTypes = array('Frontend', 'FrontendAjax');

    /**
     * @param string $type The type of init to load, possible values are: frontend, frontend_ajax, frontend_js.
     */
    public function initialize($type)
    {
        parent::initialize($type);

        \SpoonFilter::disableMagicQuotes();
    }

    /**
     * @inheritdoc
     */
    protected function definePaths()
    {
        // general paths
        defined('FRONTEND_PATH') || define('FRONTEND_PATH', PATH_WWW . '/src/Frontend');
        defined('FRONTEND_CACHE_PATH') || define('FRONTEND_CACHE_PATH', FRONTEND_PATH . '/Cache');
        defined('FRONTEND_CORE_PATH') || define('FRONTEND_CORE_PATH', FRONTEND_PATH . '/Core');
        defined('FRONTEND_MODULES_PATH') || define('FRONTEND_MODULES_PATH', FRONTEND_PATH . '/Modules');
        defined('FRONTEND_FILES_PATH') || define('FRONTEND_FILES_PATH', FRONTEND_PATH . '/Files');
    }

    /**
     * @inheritdoc
     */
    protected function defineURLs()
    {
        defined('FRONTEND_CORE_URL') || define('FRONTEND_CORE_URL', '/src/' . APPLICATION . '/Core');
        defined('FRONTEND_CACHE_URL') || define('FRONTEND_CACHE_URL', '/src/' . APPLICATION . '/Cache');
        defined('FRONTEND_FILES_URL') || define('FRONTEND_FILES_URL', '/src/' . APPLICATION . '/Files');
    }
}
