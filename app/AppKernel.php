<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;

// hardcoded this for now, this should be autoloaded
require_once __DIR__ . '/Kernel.php';
require_once __DIR__ . '/routing.php';

/**
 * The AppKernel provides a proper way to handle a request and transform it into a response.
 *
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 * @author Wouter Sioen <wouter.sioen@wijs.be>
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
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
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

        if ($fs->exists(__DIR__.'/config/config_'.$this->getEnvironment().'.yml')) {
            $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
        }
    }
}
