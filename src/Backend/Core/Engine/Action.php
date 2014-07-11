<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\KernelInterface;

use Backend\Core\Engine\Base\Action as BackendBaseAction;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This class is the real code, it creates an action, loads the config file, ...
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class Action extends Base\Object
{
    /**
     * The config file
     *
     * @var    Base\Config
     */
    private $config;

    /**
     * BackendTemplate
     *
     * @var    Template
     */
    public $tpl;

    /**
     * You have to specify the action and module so we know what to do with this instance
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        // grab stuff from the reference and store them in this object (for later/easy use)
        $this->tpl = $this->getContainer()->get('template');
    }

    /**
     * Execute the action
     * We will build the classname, require the class and call the execute method.
     */
    public function execute()
    {
        $this->loadConfig();

        // is the requested action possible? If not we throw an exception.
        // We don't redirect because that could trigger a redirect loop
        if (!in_array($this->getAction(), $this->config->getPossibleActions())) {
            throw new Exception('This is an invalid action (' . $this->getAction() . ').');
        }

        // build action-class
        $actionClass = 'Backend\\Modules\\' . $this->getModule() . '\\Actions\\' . $this->getAction();
        if ($this->getModule() == 'Core') {
            $actionClass = 'Backend\\Core\\Actions\\' . $this->getAction();
        }

        // validate if class exists (aka has correct name)
        if (!class_exists(
            $actionClass
        )
        ) {
            throw new Exception('The actionfile is present, but the classname should be: ' . $actionClass . '.');
        }

        // get working languages
        $languages = Language::getWorkingLanguages();
        $workingLanguages = array();

        // loop languages and build an array that we can assign
        foreach ($languages as $abbreviation => $label) {
            $workingLanguages[] = array(
                'abbr' => $abbreviation,
                'label' => $label,
                'selected' => ($abbreviation == Language::getWorkingLanguage())
            );
        }

        // assign the languages
        $this->tpl->assign('workingLanguages', $workingLanguages);

        // create action-object
        /** @var $object BackendBaseAction */
        $object = new $actionClass($this->getKernel());
        $this->getContainer()->get('logger')->info(
            "Executing backend action '{$object->getAction()}' for module '{$object->getModule()}'."
        );
        $object->execute();

        return $object->getContent();
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
        if ($this->getModule() == 'Core') {
            $configClass = 'Backend\\Core\\Config';
        }

        // validate if class exists (aka has correct name)
        if (!class_exists(
            $configClass
        )
        ) {
            throw new Exception('The config file is present, but the classname should be: ' . $configClass . '.');
        }

        // create config-object, the constructor will do some magic
        $this->config = new $configClass($this->getKernel(), $this->getModule());

        // set action
        $action = ($this->config->getDefaultAction() !== null) ? $this->config->getDefaultAction() : 'Index';
    }
}
