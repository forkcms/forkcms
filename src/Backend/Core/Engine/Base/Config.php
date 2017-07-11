<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Engine\Exception as BackendException;
use Backend\Core\Engine\Model as BackendModel;

/**
 * This is the base-object for config-files. The module-specific config-files
 * can extend the functionality from this class
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
    protected $disabledActions = [];

    /**
     * The disabled AJAX-actions
     *
     * @var array
     */
    protected $disabledAJAXActions = [];

    /**
     * All the possible actions
     *
     * @var array
     */
    protected $possibleActions = [];

    /**
     * All the possible AJAX actions
     *
     * @var array
     */
    protected $possibleAJAXActions = [];

    public function __construct(KernelInterface $kernel, string $module)
    {
        parent::__construct($kernel);

        $this->isModuleAvailable($module);
    }

    public function getDefaultAction(): string
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
    private function isModuleAvailable(string $module): void
    {
        // does this module exist?
        $modules = BackendModel::getModulesOnFilesystem();
        if (!in_array($module, $modules, true)) {
            // set correct headers
            header('HTTP/1.1 403 Forbidden');

            // throw exception
            throw new BackendException('Module not allowed.');
        }
    }

    public function getPossibleActionTypes(): array
    {
        return [
            'actions',
            'ajax',
        ];
    }

    public function isActionAvailable(string $action): bool
    {
        // Loop over every action type
        foreach ($this->getPossibleActionTypes() as $actionType) {
            // If this action is disabled for this type, continue on to the next type
            if ($this->isActionDisabled($actionType, $action)) {
                continue;
            }

            // If the action file is missing, continue on to the next type
            if (!$this->isActionFilePresent($actionType, $action)) {
                continue;
            }

            // The action is not disabled and the file is present, this is an available action!
            return true;
        }

        // If no types contain an available action, the action is unavailable
        return false;
    }

    private function isActionDisabled(string $actionType, string $action): bool
    {
        switch ($actionType) {
            case 'actions':
                return in_array($action, $this->disabledActions, true);
            case 'ajax':
                return in_array($action, $this->disabledAJAXActions, true);
        }

        throw new \Exception($actionType . ' is not a valid action type');
    }

    private function isActionFilePresent(string $actionType, string $action): bool
    {
        // Create the directory string
        $directory = BACKEND_MODULES_PATH . '/' . $this->module . '/' . ucfirst($actionType);

        // If the directory doesn't exist, surely the action can't exist.
        if (!is_dir($directory)) {
            return false;
        }

        // Return if the file exists
        return file_exists($directory . '/' . ucfirst($action) . '.php');
    }
}
