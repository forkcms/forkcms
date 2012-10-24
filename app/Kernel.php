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
	 * @param string $environment
	 */
	public function loadEnvironmentConfiguration($environment)
	{
		// @todo
	}
}
