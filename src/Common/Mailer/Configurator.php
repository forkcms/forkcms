<?php

namespace Common\Mailer;

use PDOException;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Common\ModulesSettings;

class Configurator
{
    /**
     * @var ModulesSettings
     */
    private $modulesSettings;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Configurator constructor.
     *
     * @param ModulesSettings    $modulesSettings
     * @param ContainerInterface $container
     */
    public function __construct(ModulesSettings $modulesSettings, ContainerInterface $container)
    {
        $this->modulesSettings = $modulesSettings;
        $this->container = $container;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->configureMail();
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $this->configureMail();
    }

    private function configureMail()
    {
        try {
            $transport = TransportFactory::create(
                (string) $this->modulesSettings->get('Core', 'mailer_type', 'mail'),
                $this->modulesSettings->get('Core', 'smtp_server'),
                (int) $this->modulesSettings->get('Core', 'smtp_port', 25),
                $this->modulesSettings->get('Core', 'smtp_username'),
                $this->modulesSettings->get('Core', 'smtp_password'),
                $this->modulesSettings->get('Core', 'smtp_secure_layer')
            );
            $this->container->set(
                'swiftmailer.mailer.default.transport',
                $transport
            );
        } catch (PDOException $e) {
            // we'll just use the mail transport thats pre-configured
        }
    }
}
