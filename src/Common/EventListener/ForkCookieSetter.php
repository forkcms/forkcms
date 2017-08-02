<?php

namespace Common\EventListener;

use Common\Core\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ForkCookieSetter
{
    /** @var Cookie */
    private $cookie;

    public function __construct(Cookie $cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * Add the fork cookies to the response
     *
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event): void
    {
        $this->cookie->attachToResponse($event->getResponse());
    }
}
