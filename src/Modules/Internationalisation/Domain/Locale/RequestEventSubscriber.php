<?php

namespace ForkCMS\Modules\Internationalisation\Domain\Locale;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['mainRequestLocale', 16]
        ];
    }

    public function mainRequestLocale(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        Locale::current(Locale::from($event->getRequest()->getLocale()));
    }
}
