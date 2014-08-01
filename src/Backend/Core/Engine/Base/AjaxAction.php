<?php

namespace Backend\Core\Engine\Base;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

/**
 * This class implements a lot of functionality that can be extended by a specific AJAX action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class AjaxAction extends Object
{
    const OK = 200;
    const BAD_REQUEST = 400;
    const FORBIDDEN = 403;
    const ERROR = 500;

    /**
     * Execute the action
     *
     * @return Response
     */
    public function execute()
    {
        return $this->getContent();
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
     * Output an answer to the browser
     *
     * @param int    $statusCode The status code for the response, use the
     *                           available constants:
     *                           self::OK, self::BAD_REQUEST, self::FORBIDDEN, self::ERROR
     * @param mixed  $data       The data to output.
     * @param string $message    The text-message to send.
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
}
