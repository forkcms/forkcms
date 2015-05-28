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
    public function onKernelResponse(FilterResponseEvent $event)
    {
        // provides clickjacking protection
        $event->getResponse()->headers->set('X-Frame-Options', 'deny');

        // enables the XSS filter built into most recent browsers
        $event->getResponse()->headers->set('X-XSS-Protection', '1; mode=block');

        // prevents IE and Chrome from MIME-sniffing
        $event->getResponse()->headers->set('X-Content-Type-Options', 'nosniff');
    }
}
