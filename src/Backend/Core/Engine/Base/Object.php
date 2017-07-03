<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Exception;
use Common\Exception\RedirectException;

/**
 * This class will be the base of the objects used in the cms
 */
class Object extends KernelLoader
{
    /**
     * The current action
     *
     * @var string
     */
    protected $action;

    /**
     * The current module
     *
     * @var string
     */
    protected $module;

    public function getAction(): string
    {
        return $this->action;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function setAction(string $action, string $module = null): void
    {
        // set module
        if ($module !== null) {
            $this->setModule($module);
        }

        // check if module is set
        if ($this->getModule() === null) {
            throw new Exception('Module has not yet been set.');
        }

        // is this action allowed?
        if (!Authentication::isAllowedAction($action, $this->getModule())) {
            // set correct headers
            header('HTTP/1.1 403 Forbidden');

            // throw exception
            throw new Exception('Action not allowed.');
        }

        // set property
        $this->action = \SpoonFilter::toCamelCase($action);
    }

    public function setModule(string $module): void
    {
        // is this module allowed?
        if (!Authentication::isAllowedModule($module)) {
            // set correct headers
            header('HTTP/1.1 403 Forbidden');

            // throw exception
            throw new Exception('Module not allowed.');
        }

        // set property
        $this->module = $module;
    }
}
