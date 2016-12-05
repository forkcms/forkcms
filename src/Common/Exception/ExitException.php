<?php

namespace Common\Exception;

use Symfony\Component\HttpFoundation\Response;

final class ExitException extends RedirectException
{
    /**
     * @param string $message
     * @param string $html
     * @param int $statusCode
     */
    public function __construct($message, $html = null, $statusCode = Response::HTTP_OK)
    {
        parent::__construct(
            $message,
            new Response(
                $html,
                $statusCode
            )
        );
    }
}
