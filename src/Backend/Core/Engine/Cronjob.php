<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Base\Object;

use Frontend\Core\Engine\Language as FrontendLanguage;

/**
 * This class will handle cronjob related stuff
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class Cronjob extends Object implements \ApplicationInterface
{
    /**
     * @var Base\Cronjob
     */
    private $cronjob;

    /**
     * @var    string
     */
    private $language;

    /**
     * @return Response
     */
    public function display()
    {
        $this->cronjob->execute();

        // a cronjob does not have output, so we return a empty string as response
        // this is not a correct solution, in time cronjobs should have there own frontcontroller.
        return new Response('');
    }

    /**
     * Execute the action
     * We will build the classname, require the class and call the execute method.
     */
    protected function execute()
    {
        if (extension_loaded('newrelic')) {
            newrelic_background_job();
        }
        $this->loadConfig();

        // build action-class-name
        $actionClass = 'Backend\\Modules\\' . $this->getModule() . '\\Cronjobs\\' . $this->getAction();
        if ($this->getModule() == 'Core') {
            $actionClass = 'Backend\\Core\\Cronjobs\\' . $this->getAction();
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($actionClass)) {
            // set correct headers
            \SpoonHTTP::setHeadersByCode(500);

            // throw exception
            throw new Exception('The cronjobfile is present, but the classname should be: ' . $actionClass . '.');
        }

        // create action-object
        $this->cronjob = new $actionClass($this->getKernel());
        $this->cronjob->setModule($this->getModule());
        $this->cronjob->setAction($this->getAction());

        if (extension_loaded('newrelic')) {
            newrelic_name_transaction('cronjob::' . $this->getModule() . '::' . $this->getAction());
        }
    }

    /**
     * This method exists because the service container needs to be set before
     * the page's functionality gets loaded.
     */
    public function initialize()
    {
        // because some cronjobs will be run on the command line we should pass parameters
        if (isset($_SERVER['argv'])) {
            // init var
            $first = true;

            // loop all passes arguments
            foreach ($_SERVER['argv'] as $parameter) {
                // ignore first, because this is the scripts name.
                if ($first) {
                    // reset
                    $first = false;

                    // skip
                    continue;
                }

                // split into chunks
                $chunks = explode('=', $parameter, 2);

                // valid parameters?
                if (count($chunks) == 2) {
                    // build key and value
                    $key = trim($chunks[0], '--');
                    $value = $chunks[1];

                    // set in GET
                    if ($key != '' && $value != '') {
                        $_GET[$key] = $value;
                    }
                }
            }
        }

        // define the Named Application
        if (!defined('NAMED_APPLICATION')) {
            define('NAMED_APPLICATION', 'Backend');
        }

        // set the module
        $this->setModule(\SpoonFilter::toCamelCase(\SpoonFilter::getGetValue('module', null, '')));

        // set the requested file
        $this->setAction(\SpoonFilter::toCamelCase(\SpoonFilter::getGetValue('action', null, '')));

        // set the language
        $this->setLanguage(
            \SpoonFilter::getGetValue('language', FrontendLanguage::getActiveLanguages(), SITE_DEFAULT_LANGUAGE)
        );

        // mark cronjob as run
        $cronjobs = (array) BackendModel::getModuleSetting('Core', 'cronjobs');
        $cronjobs[] = $this->getModule() . '.' . $this->getAction();
        BackendModel::setModuleSetting('Core', 'cronjobs', array_unique($cronjobs));

        $this->execute();
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Load the config file for the requested module.
     * In the config file we have to find disabled actions, the constructor
     * will read the folder and set possible actions
     * Other configurations will be stored in it also.
     */
    public function loadConfig()
    {
        // check if module path is not yet defined
        if (!defined('BACKEND_MODULE_PATH')) {
            // build path for core
            if ($this->getModule() == 'Core') {
                define('BACKEND_MODULE_PATH', BACKEND_PATH . '/' . $this->getModule());
            } else {
                // build path to the module and define it. This is a constant because we can use this in templates.
                define('BACKEND_MODULE_PATH', BACKEND_MODULES_PATH . '/' . $this->getModule());
            }
        }

        // check if we can load the config file
        $configClass = 'Backend\\Modules\\' . $this->getModule() . '\\Config';

        // is the Core module
        if ($this->getModule() == 'Core') {
            $configClass = 'Backend\\Core\\Config';
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($configClass)) {
            throw new Exception('The config file is present, but the classname should be: ' . $configClass . '.');
        }

        // create config-object, the constructor will do some magic
        $this->config = new $configClass($this->getKernel(), $this->getModule());

        // set action
        $action = ($this->config->getDefaultAction() !== null) ? $this->config->getDefaultAction() : 'Index';
    }

    /**
     * Set the action
     *
     * We can't rely on the parent setModule function, because a cronjob requires no login
     *
     * @param string $action The action to load.
     * @param string $module The module to load.
     */
    public function setAction($action, $module = null)
    {
        // set module
        if ($module !== null) {
            $this->setModule($module);
        }

        // check if module is set
        if ($this->getModule() === null) {
            throw new Exception('Module has not yet been set.');
        }

        // set property
        $this->action = (string) $action;
    }

    /**
     * Set language
     *
     * @param string $value The language to load.
     */
    public function setLanguage($value)
    {
        // get the possible languages
        $possibleLanguages = Language::getWorkingLanguages();

        // validate
        if (!in_array($value, array_keys($possibleLanguages))) {
            throw new Exception('Invalid language.');
        }

        // set property
        $this->language = $value;

        // set the locale (we need this for the labels)
        Language::setLocale($this->language);

        // set working language
        Language::setWorkingLanguage($this->language);
    }

    /**
     * Set the module
     *
     * We can't rely on the parent setModule function, because a cronjob requires no login
     *
     * @param string $module The module to load.
     */
    public function setModule($module)
    {
        // does this module exist?
        $modules = BackendModel::getModulesOnFilesystem();
        if (!in_array($module, $modules)) {
            // set correct headers
            \SpoonHTTP::setHeadersByCode(403);

            // throw exception
            throw new Exception('Module not allowed.');
        }

        // set property
        $this->module = $module;
    }
}
