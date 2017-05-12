<?php

namespace Frontend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Frontend\Core\Config;
use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Symfony\Component\HttpFoundation\Response;

/**
 * FrontendAJAXAction
 */
class AjaxAction extends FrontendBaseAJAXAction
{
    /**
     * The config file
     *
     * @var Base\Config
     */
    protected $config;

    /**
     * Execute the action.
     * We will build the class name, require the class and call the execute method
     *
     * @return Response
     */
    public function getContent(): Response
    {
        $this->loadConfig();

        $actionClass = 'Frontend\\Modules\\' . $this->getModule() . '\\Ajax\\' . $this->getAction();
        if ($this->getModule() === 'Core') {
            $actionClass = 'Frontend\\Core\\Ajax\\' . $this->getAction();
        }

        if (!class_exists($actionClass)) {
            throw new Exception('The action file ' . $actionClass . ' could not be found.');
        }

        /** @var FrontendBaseAJAXAction $ajaxAction */
        $ajaxAction = new $actionClass($this->getKernel(), $this->getAction(), $this->getModule());
        $ajaxAction->execute();

        return $ajaxAction->getContent();
    }

    /**
     * Load the config file for the requested module.
     * In the config file we have to find disabled actions, the constructor
     * will read the folder and set possible actions.
     * Other configurations will also be stored in it.
     */
    public function loadConfig(): void
    {
        $configClass = 'Frontend\\Modules\\' . $this->getModule() . '\\Config';
        if ($this->getModule() === 'Core') {
            $configClass = Config::class;
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($configClass)) {
            throw new Exception('The config file ' . $configClass . ' could not be found.');
        }

        // create config-object, the constructor will do some magic
        $this->config = new $configClass($this->getKernel(), $this->getModule());
    }
}
