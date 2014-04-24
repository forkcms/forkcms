<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Exception;

/**
 * This class will be the base of the objects used in the cms
 *
 * @author Matthias Mullie <forkcms@mullie.eu>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class Object extends \KernelLoader
{
    /**
     * The current action
     *
     * @var    string
     */
    protected $action;

    /**
     * The actual output
     *
     * @var string
     */
    protected $content;

    /**
     * The current module
     *
     * @var    string
     */
    protected $module;

    /**
     * Get the action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set the action
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

        // is this action allowed?
        if (!Authentication::isAllowedAction($action, $this->getModule())) {
            // set correct headers
            \SpoonHTTP::setHeadersByCode(403);

            // throw exception
            throw new Exception('Action not allowed.');
        }

        // set property
        $this->action = (string) \SpoonFilter::toCamelCase($action);
    }

    /**
     * Set the module
     *
     * @param string $module The module to load.
     */
    public function setModule($module)
    {
        // is this module allowed?
        if (!Authentication::isAllowedModule($module)) {
            // set correct headers
            \SpoonHTTP::setHeadersByCode(403);

            // throw exception
            throw new Exception('Module not allowed.');
        }

        // set property
        $this->module = $module;
    }

    /**
     * Since the display action in the backend is rather complicated and we
     * want to make this work with our Kernel, I've added this getContent
     * method to extract the output from the actual displaying.
     *
     * With this function we'll be able to get the content and return it as a
     * Symfony output object.
     *
     * @return Response
     */
    public function getContent()
    {
        return new Response(
            $this->content,
            200
        );
    }
}
