<?php

namespace Frontend\Core\Engine\Base;

use ForkCMS\App\KernelLoader;
use Frontend\Core\Engine\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class implements a lot of functionality that can be extended by a specific AJAX action
 */
class AjaxAction extends KernelLoader
{
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

    public function __construct(KernelInterface $kernel, string $action, string $module)
    {
        parent::__construct($kernel);

        // store the current module and action (we grab them from the URL)
        $this->setModule($module);
        $this->setAction($action);
    }

    public function execute(): void
    {
        // placeholder
    }

    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Since the display action in the frontend is rather complicated and we
     * want to make this work with our Kernel, I've added this getContent
     * method to extract the output from the actual displaying.
     *
     * With this function we'll be able to get the content and return it as a
     * Symfony output object.
     *
     * @return Response
     */
    public function getContent(): Response
    {
        return new Response(
            json_encode($this->content),
            $this->content['code'] ?? Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * Outputs an answer to the browser
     *
     * @param int $statusCode The status code to use, use one of the available constants
     *                           (self::OK, self::BAD_REQUEST, self::FORBIDDEN, self::ERROR).
     * @param mixed $data The data to be returned (will be encoded as JSON).
     * @param string $message A text-message.
     */
    public function output(int $statusCode, $data = null, string $message = null): void
    {
        $this->content = ['code' => $statusCode, 'data' => $data, 'message' => $message];
    }

    protected function setAction(string $action): void
    {
        $this->action = $action;
    }

    protected function setModule(string $module): void
    {
        $this->module = $module;
    }

    /**
     * Get the request from the container.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return Model::getRequest();
    }
}
