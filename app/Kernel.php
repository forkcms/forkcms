<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * The Kernel provides a proper way to load an environment and DI container.
 * It also handles requests and responses.
 *
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
abstract class Kernel implements HttpKernelInterface
{
	/**
	 * @var ContainerBuilder
	 */
	protected $container;

	/**
	 * @var bool
	 */
	protected $debug;

	/**
	 * To mirror symfony, $environment should not be optional, but for now we have no reason
	 * to actually do this because we can't use the profiler.
	 *
	 * Debugging is added to mirror Symfony, but does not actually do anything at this moment,
	 * I used it to fill up the container with some kernel settings.
	 *
	 * @param string[optional] $environment
	 * @param bool[optional] $debug
	 */
	public function __construct($environment = null, $debug = false)
	{
		$this->environment = $environment;
		$this->debug = $debug;

		require_once PATH_WWW . '/library/globals.php';

		if($environment !== null)
		{
			$this->loadEnvironmentConfiguration($this->environment);
		}

		$this->initializeContainer();
	}

	/**
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * @return ContainerBuilder
	 */
	protected function getContainerBuilder()
	{
		return new ContainerBuilder(new ParameterBag($this->getKernelParameters()));
	}

	/**
	 * @return string
	 */
	public function getEnvironment()
	{
		return $this->environment;
	}

	/**
	 * @return array
	 */
	public function getKernelParameters()
	{
		// @todo load names of active bundles

		return array(
			'kernel.debug' => $this->debug, // @todo in time, remove SPOON_DEBUG
			'kernel.environment' => $this->environment,
			// @todo moar info (paths, list of bundles,...)
		);
	}

	/**
	 * This will load a cached version of the service container, or build one from scratch.
	 */
	public function initializeContainer()
	{
		$this->container = $this->getContainerBuilder();

		// load parameters config
		$this->container->setParameter('database.type', DB_TYPE);
		$this->container->setParameter('database.hostname', DB_HOSTNAME);
		$this->container->setParameter('database.port', DB_PORT);
		$this->container->setParameter('database.username', DB_USERNAME);
		$this->container->setParameter('database.password', DB_PASSWORD);
		$this->container->setParameter('database.database', DB_DATABASE);

		$this->container->register('database', 'SpoonDatabase')
						->addArgument('%database.type%')
						->addArgument('%database.hostname%')
						->addArgument('%database.username%')
						->addArgument('%database.password%')
						->addArgument('%database.database%')
						->addArgument('%database.port%');

		$this->container->get('database')->execute(
			'SET CHARACTER SET utf8, NAMES utf8, time_zone = "+0:00"'
		);
	}

	/**
	 * @param string $environment
	 */
	public function loadEnvironmentConfiguration($environment)
	{
		// @todo
	}
}
