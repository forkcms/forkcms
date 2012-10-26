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
	 * @var ApplicationRouting
	 */
	private $router;

	/**
	 * Handles a request to convert into a response.
	 * When $catch is true, the implementation must catch all exceptions
	 * and do its best to convert them to a Response instance.
	 *
	 * We intercept this object so we can load all functionality involved with Fork.
	 */
	public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
	{
		$this->router = new ApplicationRouting($request, $this);
		return $this->router->handleRequest();
	}

	/**
	 * @param LoaderInterface $load
	 */
	public function registerContainerConfiguration(LoaderInterface $loader)
	{
		// @todo this should load an environment-specific config as a replacement of our globals.php
		//$loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
		$loader->load(__DIR__ . '/config/config.yml');
	}

	/**
	 * Register our services here. This will move to bundle-level
	 * once we use the full-stack Symfony framework.
	 */
	public function registerServices()
	{
		// @todo for now, parameter loading is disabled until globals are in YAML
		$container = $this->getContainer();
		$container->setParameter('database_driver', DB_TYPE);
		$container->setParameter('database_host', DB_HOSTNAME);
		$container->setParameter('database_port', DB_PORT);
		$container->setParameter('database_user', DB_USERNAME);
		$container->setParameter('database_password', DB_PASSWORD);
		$container->setParameter('database_name', DB_DATABASE);

		$container->register('database', 'SpoonDatabase')
			->addArgument('%database_driver%')
			->addArgument('%database_host%')
			->addArgument('%database_user%')
			->addArgument('%database_password%')
			->addArgument('%database_name%')
			->addArgument('%database_port%')
			->addMethodCall(
				'execute',
				array(
					'SET CHARACTER SET :charset, NAMES :charset, time_zone = "+0:00"',
					array('charset' => 'utf8')
				)
			);
	}
}

