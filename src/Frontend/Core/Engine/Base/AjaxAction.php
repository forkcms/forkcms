<?php

namespace Frontend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use ForkCMS\App\KernelLoader;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class implements a lot of functionality that can be extended by a specific AJAX action
 */
class AjaxAction extends KernelLoader
{
    const OK = 200;
    const BAD_REQUEST = 400;
    const FORBIDDEN = 403;
    const ERROR = 500;

    /**
     * The current action
     *
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $content;

    /**
     * The current module
     *
     * @var string
     */
    protected $module;

    /**
     * @param KernelInterface $kernel
     * @param string          $action The action to use.
     * @param string          $module The module to use.
     */
    public function __construct(KernelInterface $kernel, $action, $module)
    {
        parent::__construct($kernel);

        // store the current module and action (we grab them from the URL)
        $this->setModule($module);
        $this->setAction($action);
    }

    /**
     * Execute the action
     */
    public function execute()
    {
        return $this->getContent();
    }

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
        $statusCode = (isset($this->content['code']) ? $this->content['code'] : 200);

        return new Response(
            json_encode($this->content),
            $statusCode,
            array('content-type' => 'application/json')
        );
    }

    /**
     * Get the module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Outputs an answer to the browser
     *
     * @param int    $statusCode The status code to use, use one of the available constants
     *                           (self::OK, self::BAD_REQUEST, self::FORBIDDEN, self::ERROR).
     * @param mixed  $data       The data to be returned (will be encoded as JSON).
     * @param string $message    A text-message.
     */
    public function output($statusCode, $data = null, $message = null)
    {
        $statusCode = (int) $statusCode;
        if ($message !== null) {
            $message = (string) $message;
        }

        $response = array('code' => $statusCode, 'data' => $data, 'message' => $message);

        $this->content = $response;
    }

    /**
     * Set the action, for later use
     *
     * @param string $action The action to use.
     */
    protected function setAction($action)
    {
        $this->action = (string) $action;
    }

    /**
     * Set the module, for later use
     *
     * @param string $module The module to use.
     */
    protected function setModule($module)
    {
        $this->module = (string) $module;
    }
}
