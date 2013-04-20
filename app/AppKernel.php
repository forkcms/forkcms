<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Loader\LoaderInterface;

// hardcode this for now, this should be autoloaded
require_once __DIR__ . '/Kernel.php';
require_once __DIR__ . '/routing.php';

/**
 * The AppKernel provides a proper way to handle a request and transform it into a response.
 *
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class AppKernel extends Kernel
{
	/**
	 * Register all the bundles we'll be using.
	 *
	 * @return array
	 */
	public function registerBundles()
	{
		return array(
			new Symfony\Bundle\MonologBundle\MonologBundle(),
		);
	}

	/**
	 * @param LoaderInterface $loader
	 */
	public function registerContainerConfiguration(LoaderInterface $loader)
	{
		// load the general config.yml
		$loader->load(__DIR__ . '/config/config.yml');
	}
}
