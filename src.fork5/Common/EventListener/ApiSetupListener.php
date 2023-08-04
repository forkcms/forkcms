<?php

namespace Common\EventListener;

use Backend\Core\Engine\Backend;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

final class ApiSetupListener
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (strpos($event->getRequest()->getPathInfo(), '/api/') === 0) {
            $application = new Backend($this->container->get('kernel'));
            $application->passContainerToModels();
        }
    }
}
