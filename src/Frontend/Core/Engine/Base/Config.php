<?php

namespace Frontend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * This is the base-object for config-files.
 * The module-specific config-files can extend the functionality from this class.
 */
class Config extends KernelLoader
{
    /**
     * The default action
     *
     * @var string
     */
    protected $defaultAction = 'Index';

    /**
     * The disabled actions
     *
     * @var array
     */
    protected $disabledActions = array();

    /**
     * The disabled AJAX-actions
     *
     * @var array
     */
    protected $disabledAJAXActions = array();

    /**
     * The current loaded module
     *
     * @var string
     */
    protected $module;

    /**
     * All the possible actions
     *
     * @var array
     */
    protected $possibleActions = array();

    /**
     * All the possible AJAX actions
     *
     * @var array
     */
    protected $possibleAJAXActions = array();

    /**
     * @param KernelInterface $kernel
     * @param string          $module The module wherefore this is the configuration-file.
     */
    public function __construct(KernelInterface $kernel, $module)
    {
        parent::__construct($kernel);

        $this->module = (string) $module;

        // read the possible actions based on the files
        $this->setPossibleActions();
    }

    /**
     * Get the default action
     *
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * Get the current loaded module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Get the possible actions
     *
     * @return array
     */
    public function getPossibleActions()
    {
        return $this->possibleActions;
    }

    /**
     * Get the possible AJAX actions
     *
     * @return array
     */
    public function getPossibleAJAXActions()
    {
        return $this->possibleAJAXActions;
    }

    /**
     * Set the possible actions, based on files in folder.
     * You can disable action in the config file. (Populate $disabledActions)
     */
    protected function setPossibleActions()
    {
        // build path to the module
        $frontendModulePath = FRONTEND_MODULES_PATH . '/' . $this->getModule();
        $filesystem = new Filesystem();

        if ($filesystem->exists($frontendModulePath . '/Actions')) {
            // get regular actions
            $finder = new Finder();
            $finder->name('*.php');
            foreach ($finder->files()->in($frontendModulePath . '/Actions') as $file) {
                /** @var $file \SplFileInfo */
                $action = $file->getBasename('.php');
                if (!in_array($action, $this->disabledActions)) {
                    $this->possibleActions[$file->getBasename()] = $action;
                }
            }
        }

        if ($filesystem->exists($frontendModulePath . '/Ajax')) {
            // get ajax-actions
            $finder = new Finder();
            $finder->name('*.php');
            foreach ($finder->files()->in($frontendModulePath . '/Ajax') as $file) {
                /** @var $file \SplFileInfo */
                $action = $file->getBasename('.php');
                if (!in_array($action, $this->disabledAJAXActions)) {
                    $this->possibleAJAXActions[$file->getBasename()] = $action;
                }
            }
        }
    }
}
