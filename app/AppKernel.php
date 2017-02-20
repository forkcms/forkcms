<?php

namespace ForkCMS\App;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The AppKernel provides a proper way to handle a request and transform it into a response.
 */
class AppKernel extends Kernel
{
    /**
     * Load all the bundles we'll be using in our application.
     *
     * @return array
     */
    public function registerBundles()
    {
        $bundles = array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \ForkCMS\Bundle\InstallerBundle\ForkCMSInstallerBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle(),
            new \SimpleBus\SymfonyBridge\DoctrineOrmBridgeBundle(),
            new \SimpleBus\SymfonyBridge\SimpleBusEventBusBundle(),
            new \Backend\Modules\Mailmotor\Mailmotor,
            new \MailMotor\Bundle\MailMotorBundle\MailMotorMailMotorBundle(),
            new \MailMotor\Bundle\MailChimpBundle\MailMotorMailChimpBundle(),
            new \MailMotor\Bundle\CampaignMonitorBundle\MailMotorCampaignMonitorBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Symfony\Bundle\DebugBundle\DebugBundle();
        }

        return $bundles;
    }

    /**
     * @param LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $fs = new Filesystem();
        if ($fs->exists(__DIR__ . '/config/parameters.yml')) {
            $loader->load(__DIR__ . '/config/config.yml');
        }

        if ($fs->exists(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml')) {
            $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
        }
    }
}
