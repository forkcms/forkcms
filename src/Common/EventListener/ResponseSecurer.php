<?php

namespace Common\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ResponseSecurer
{
    /**
     * Add some headers to the response to make our application more secure
     * see https://www.owasp.org/index.php/List_of_useful_HTTP_headers
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event): void
    {
        $headers = [
            'X-Frame-Options' => 'deny',
            'referrer' => 'strict-origin-when-cross-origin',
            'X-XSS-Protection' => '1; mode=block',
            'X-Content-Type-Options' => 'nosniff',
        ];


        $responseHeaders = $event->getResponse()->headers;
        foreach ($headers as $header => $value) {
            if (!$responseHeaders->has($header)) {
                $responseHeaders->set($header, $value);
            }
        }

        // Don't leak server config
        $blockedHeaders = [
            'x-powered-by',
            'Server',
        ];
        foreach ($blockedHeaders as $blockedHeader) {
            if ($responseHeaders->has($blockedHeader)) {
                $responseHeaders->remove($blockedHeader);
            }
        }
    }
}
