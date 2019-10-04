<?php

namespace Common\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class ApplicationSetterListener
{
    public function onKernelRequest(GetResponseEvent $event): void
    {
        switch ($event->getRequest()->attributes->get('_route')) {
            case 'backend':
                $application = 'Backend';
                defined('NAMED_APPLICATION') || define('NAMED_APPLICATION', 'private');
                break;
            case 'backend_ajax':
                $application = 'Backend';
                break;
            case 'frontend':
            case 'frontend_ajax':
                $application = 'Frontend';
                break;
            default:
                $application = 'Backend';
        }

        defined('APPLICATION') || define('APPLICATION', $application);
    }
}
