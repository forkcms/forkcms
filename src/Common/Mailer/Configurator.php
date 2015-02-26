<?php

namespace Common\Mailer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Frontend\Core\Engine\Model;

class Configurator implements EventSubscriberInterface
{
    private $database;
    private $container;
    private $settings;

    public function __construct($database, $container)
    {
        $this->database = $database;
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $transport = \Common\Mailer\TransportFactory::create(
            $this->getMailSetting('mailer_type', 'mail'),
            $this->getMailSetting('smtp_server'),
            $this->getMailSetting('smtp_port', 25),
            $this->getMailSetting('smtp_username'),
            $this->getMailSetting('smtp_password'),
            $this->getMailSetting('smtp_secure_layer')
        );
        $this->container->set(
            'mailer',
            \Swift_Mailer::newInstance($transport)
        );
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 0)
        );
    }

    private function getMailSetting($name, $default = null)
    {
        if (empty($this->settings)) {
            $this->settings = $this->database->getPairs(
                'SELECT name, value
                 FROM modules_settings
                 WHERE module = :core',
                array('core' => 'Core')
            );
        }

        if (isset($this->settings[$name])) {
            return unserialize($this->settings[$name]);
        }

        return $default;
    }
}
