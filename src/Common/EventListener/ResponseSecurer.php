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
            'X-XSS-Protection' => '1; mode=block',
            'X-Content-Type-Options' => 'nosniff',
        ];

        foreach ($headers as $header => $value) {
            if (!$event->getResponse()->headers->has($header)) {
                $event->getResponse()->headers->set($header, $value);
            }
        }
    }
}
