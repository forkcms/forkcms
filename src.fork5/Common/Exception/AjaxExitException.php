<?php

namespace Common\Exception;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class AjaxExitException extends RedirectException
{
    /**
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     */
    public function __construct(string $message, $data = null, int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct(
            $message,
            new JsonResponse(
                [
                    'code' => $statusCode,
                    'data' => $data,
                    'message' => $message,
                ],
                $statusCode
            )
        );
    }
}
