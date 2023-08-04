<?php

namespace ForkCMS\App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The AppKernel provides a proper way to handle a request and transform it into a response.
 */
class AppKernel extends Kernel
{
    /**
     * Load all the bundles we'll be using in our application.
     */
    public function registerBundles(): array
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \ForkCMS\Bundle\InstallerBundle\ForkCMSInstallerBundle(),
            new \ForkCMS\Bundle\CoreBundle\ForkCMSCoreBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle(),
            new \SimpleBus\SymfonyBridge\DoctrineOrmBridgeBundle(),
            new \SimpleBus\SymfonyBridge\SimpleBusEventBusBundle(),
            new \Backend\Modules\MediaLibrary\MediaLibrary(),
            new \Backend\Modules\Mailmotor\Mailmotor(),
            new \MailMotor\Bundle\MailMotorBundle\MailMotorMailMotorBundle(),
            new \MailMotor\Bundle\MailChimpBundle\MailMotorMailChimpBundle(),
            new \MailMotor\Bundle\CampaignMonitorBundle\MailMotorCampaignMonitorBundle(),
            new \Liip\ImagineBundle\LiipImagineBundle(),
            new \FOS\RestBundle\FOSRestBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \Symfony\WebpackEncoreBundle\WebpackEncoreBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $fs = new Filesystem();

        if ($fs->exists(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml')) {
            $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
        }
    }
}
