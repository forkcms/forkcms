<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Loader\LoaderInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;

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
	public function __construct()
	{
		parent::__construct('dev', true);
		AnnotationRegistry::registerFile(
			__DIR__ . "/../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php"
		);
	}

	/**
	 * Load all the bundles we'll be using in our application.
	 *
	 * @return array
	 */
	public function registerBundles()
	{
		$bundles = array(
			// Symfony dependencies
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new Symfony\Bundle\TwigBundle\TwigBundle(),
			new Symfony\Bundle\MonologBundle\MonologBundle(),
			new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
			new Symfony\Bundle\AsseticBundle\AsseticBundle(),
			new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

			// Fork dependencies
			new ForkCMS\Bundle\ContentBlocksBundle\ContentBlocksBundle(),
		);

		return $bundles;
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
