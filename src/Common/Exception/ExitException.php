<?php

namespace Common\Exception;

use Symfony\Component\HttpFoundation\Response;

final class ExitException extends RedirectException
{
    public function __construct(string $message, string $html = null, int $statusCode = Response::HTTP_OK)
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
