<?php

namespace Backend\Core\Engine\Base;

use Backend\Core\Engine\Exception as BackendException;
use Backend\Core\Engine\Model as BackendModel;
use ForkCMS\App\KernelLoader;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\KernelInterface;
use Backend\Core\Config as CoreConfig;

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

    /**
     * @var string
     */
    private $module;

    public function __construct(KernelInterface $kernel, string $module)
    {
        parent::__construct($kernel);

        $this->setModule($module);
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
    private function setModule(string $module): void
    {
        // does this module exist?
        $modules = BackendModel::getModulesOnFilesystem();
        if (!in_array($module, $modules, true)) {
            // set correct headers
            header('HTTP/1.1 403 Forbidden');

            // throw exception
            throw new BackendException('Module not allowed.');
        }

        $this->module = $module;
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
            if (!$this->isActionAvailableForActionType($action, $actionType)) {
                continue;
            }

            // The action is not disabled and the file is present, this is an available action!
            return true;
        }

        // If no types contain an available action, the action is unavailable
        return false;
    }

    public function isActionAvailableForActionType(string $action, string $actionType): bool
    {
        // The action is disabled
        if ($this->isActionDisabled($actionType, $action)) {
            return false;
        }

        // The action class is missing
        if (!$this->actionClassExists($actionType, $action)) {
            return false;
        }

        // The action is not disabled and the file is present, this is an available action!
        return true;
    }

    private function isActionDisabled(string $actionType, string $action): bool
    {
        switch ($actionType) {
            case 'actions':
                return in_array($action, $this->disabledActions, true);
            case 'ajax':
                return in_array($action, $this->disabledAJAXActions, true);
        }

        throw new InvalidArgumentException($actionType . ' is not a valid action type');
    }

    private function actionClassExists(string $actionType, string $action): bool
    {
        $actionClass = $this->buildActionClass($actionType, $action);

        return class_exists($actionClass);
    }

    public function getActionClass(string $actionType, string $action): string
    {
        $actionClass = $this->buildActionClass($actionType, $action);

        if (!$this->actionClassExists($actionType, $action)) {
            throw new InvalidArgumentException('The class ' . $actionClass . ' could not be found.');
        }

        return $actionClass;
    }

    private function buildActionClass(string $actionType, string $action): string
    {
        $actionType = ucfirst($actionType);

        $actionClass = 'Backend\\Modules\\' . $this->module . '\\' . $actionType . '\\' . $action;
        if ($this->module === 'Core' && $actionType === 'Ajax') {
            $actionClass = 'Backend\\Core\\Ajax\\' . $action;
        }

        return $actionClass;
    }

    /**
     * Get the config file for the requested module.
     * In the config file we have to find disabled actions, the constructor
     * will read the folder and set possible actions
     * Other configurations will be stored in it also.
     *
     * @param KernelInterface $kernel
     * @param string $module
     *
     * @return self
     */
    public static function forModule(KernelInterface $kernel, string $module): self
    {
        $configClass = 'Backend\\Modules\\' . $module . '\\Config';
        if ($module === 'Core') {
            $configClass = CoreConfig::class;
        }

        // validate if class exists (aka has correct name)
        if (!class_exists($configClass)) {
            throw new InvalidArgumentException('The config file ' . $configClass . ' could not be found.');
        }

        return new $configClass($kernel, $module);
    }
}
