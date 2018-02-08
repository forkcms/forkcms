<?php

namespace App\Service\Mailer;

use PDOException;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Service\Module\ModuleSettings;

class Configurator
{
    /**
     * @var ModuleSettings
     */
    private $moduleSettings;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ModuleSettings $moduleSettings, ContainerInterface $container)
    {
        $this->moduleSettings = $moduleSettings;
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $this->configureMail();
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $this->configureMail();
    }

    private function configureMail(): void
    {
        try {
            $transport = TransportFactory::create(
                (string) $this->moduleSettings->get('Core', 'mailer_type', 'sendmail'),
                $this->moduleSettings->get('Core', 'smtp_server'),
                (int) $this->moduleSettings->get('Core', 'smtp_port', 25),
                $this->moduleSettings->get('Core', 'smtp_username'),
                $this->moduleSettings->get('Core', 'smtp_password'),
                $this->moduleSettings->get('Core', 'smtp_secure_layer')
            );
            $mailer = $this->container->get('mailer');
            if ($mailer !== null) {
                $this->container->get('mailer')->__construct($transport);
            }
            $this->container->set(
                'swiftmailer.transport',
                $transport
            );
        } catch (PDOException $e) {
            // we'll just use the mail transport thats pre-configured
        }
    }
}
