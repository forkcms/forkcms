<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\Config;
use Backend\Core\Config as CoreConfig;
use Backend\Core\Engine\Base\AjaxAction as BaseAjaxAction;
use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class is the real code, it creates an action, loads the config file, ...
 */
final class AjaxAction extends KernelLoader
{
    /**
     * @var BaseAjaxAction
     */
    private $ajaxAction;

    public function display(): Response
    {
        $this->ajaxAction->execute();

        return $this->ajaxAction->getContent();
    }

    public function __construct(KernelInterface $kernel, string $module, string $action)
    {
        parent::__construct($kernel);

        $config = $this->getModuleConfig($module);
        $actionClass = $config->getActionClass('ajax', $action);

        $this->ajaxAction = new $actionClass($this->getKernel());
    }

    /**
     * Load the config file for the requested module.
     * In the config file we have to find disabled actions, the constructor
     * will read the folder and set possible actions
     * Other configurations will be stored in it also.
     *
     * @param string $module
     *
     * @return Config
     */
    private function getModuleConfig(string $module): Config
    {
        // check if we can load the config file
        $configClass = 'Backend\\Modules\\' . $module . '\\Config';
        if ($module === 'Core') {
            $configClass = CoreConfig::class;
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($configClass)) {
            throw new Exception('The config file ' . $configClass . ' could not be found.');
        }

        return new $configClass($this->getKernel(), $module);
    }
}
