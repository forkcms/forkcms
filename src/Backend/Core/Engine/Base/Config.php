<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Engine\Exception as BackendException;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the base-object for config-files. The module-specific config-files
 * can extend the functionality from this class
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
     * Set the module
     *
     * We can't rely on the parent setModule function, because config could be
     * called before any authentication is required.
     *
     * @param string $module The module to load.
     *
     * @throws BackendException If module is not allowed
     */
    public function setModule($module)
    {
        // does this module exist?
        $modules = BackendModel::getModulesOnFilesystem();
        if (!in_array($module, $modules)) {
            // set correct headers
            header('HTTP/1.1 403 Forbidden');

            // throw exception
            throw new BackendException('Module not allowed.');
        }

        // set property
        $this->module = $module;
    }

    /**
     * @return array
     */
    public function getPossibleActionTypes()
    {
        return array(
            'actions',
            'ajax',
        );
    }

    /**
     * @param string $action
     *
     * @return bool
     */
    public function isActionAvailable($action)
    {
        // Save our action
        $this->action = $action;

        // Loop over every action type
        foreach ($this->getPossibleActionTypes() as $actionType) {
            // If this action is disabled for this type, continue on to the next type
            if ($this->isActionDisabled($actionType)) {
                continue;
            }

            // If the action file is missing, continue on to the next type
            if (!$this->isActionFilePresent($actionType)) {
                continue;
            }

            // The action is not disabled and the file is present, this is an available action!
            return true;
        }

        // If no types contain an available action, the action is unavailable
        return false;
    }

    /**
     * @param string $actionType
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function isActionDisabled($actionType)
    {
        switch ($actionType) {
            case 'actions':
                return in_array($this->getAction(), $this->disabledActions);
            case 'ajax':
                return in_array($this->action, $this->disabledAJAXActions);
        }

        throw new \Exception($actionType . ' is not a valid action type');
    }

    /**
     * @param string $actionType
     *
     * @return bool
     */
    public function isActionFilePresent($actionType)
    {
        // Create the directory string
        $directory = BACKEND_MODULES_PATH . '/' . $this->module . '/' . ucfirst($actionType);

        // If the directory doesn't exist, surely the action can't exist.
        if (!is_dir($directory)) {
            return false;
        }

        // Return if the file exists
        return file_exists($directory . '/' . ucfirst($this->action) . '.php');
    }
}
