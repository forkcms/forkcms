<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

use Backend\Core\Engine\Exception as BackendException;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the base-object for config-files. The module-specific config-files
 * can extend the functionality from this class
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class Config extends Object
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

        $this->setModule($module);

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
     * Set the module
     *
     * We can't rely on the parent setModule function, because config could be
     * called before any authentication is required.
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
            throw new BackendException('Module not allowed.');
        }

        // set property
        $this->module = $module;
    }

    /**
     * Set the possible actions, based on files in folder
     * You can disable action in the config file. (Populate $disabledActions)
     */
    public function setPossibleActions()
    {
        $path = BACKEND_MODULES_PATH . '/' . $this->getModule();
        if (is_dir($path . '/Actions')) {
            $finder = new Finder();
            foreach ($finder->files()->name('*.php')->in($path . '/Actions') as $file) {
                /** @var $file \SplFileInfo */
                $action = str_replace('.php', '', $file->getBasename());

                // if the action isn't disabled add it to the possible actions
                if (!in_array($action, $this->disabledActions)) {
                    $this->possibleActions[$file->getBasename()] = $action;
                }
            }
        }

        if (is_dir($path . '/Ajax')) {
            $finder = new Finder();
            foreach ($finder->files()->name('*.php')->in($path . '/Ajax') as $file) {
                /** @var $file \SplFileInfo */
                $action = str_replace('.php', '', $file->getBasename());

                // if the action isn't disabled add it to the possible actions
                if (!in_array($action, $this->disabledAJAXActions)) {
                    $this->possibleAJAXActions[$file->getBasename()] = $action;
                }
            }
        }
    }
}
