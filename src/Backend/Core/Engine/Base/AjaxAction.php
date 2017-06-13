<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class implements a lot of functionality that can be extended by a specific AJAX action
 */
class AjaxAction extends Object
{
    const OK = 200;
    const BAD_REQUEST = 400;
    const FORBIDDEN = 403;
    const ERROR = 500;

    public function __construct(KernelInterface $kernel, string $action = null, string $module = null)
    {
        parent::__construct($kernel);

        if ($action !== null) {
            $this->setAction($action, $module);
        }
    }

    public function execute(): void
    {
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
    public function getContent(): Response
    {
        return new Response(
            json_encode($this->content),
            $this->content['code'] ?? self::OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * Output an answer to the browser
     *
     * @param int $statusCode The status code for the response, use the
     *                           available constants:
     *                           self::OK, self::BAD_REQUEST, self::FORBIDDEN, self::ERROR
     * @param mixed $data The data to output.
     * @param string $message The text-message to send.
     */
    public function output(int $statusCode, $data = null, string $message = null): void
    {
        $this->content = ['code' => $statusCode, 'data' => $data, 'message' => $message];
    }

    /**
     * Get the request from the container.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->get('request');
    }
}
