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
                // @todo Remove constants in the future
                defined('NAMED_APPLICATION') || define('NAMED_APPLICATION', 'private');
                $event->getRequest()->attributes->set('NAMED_APPLICATION', NAMED_APPLICATION);
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

        // @todo Remove constants in the future
        defined('APPLICATION') || define('APPLICATION', $application);
        $event->getRequest()->attributes->set('APPLICATION', APPLICATION);
    }
}
