<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;

/**
 * The Kernel provides a proper way to load an environment and DI container.
 * It also handles requests and responses.
 *
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
abstract class Kernel implements KernelInterface
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
	protected function getKernelParameters()
	{
		// @todo load names of active bundles

		return array(
			'kernel_debug' => $this->debug, // @todo in time, remove SPOON_DEBUG
			'kernel_environment' => $this->environment,
			'kernel_server_protocol' => (strpos($_SERVER['SERVER_PROTOCOL'], 'HTTPS') === false),
			// @todo moar info (paths, list of bundles,...)
		);
	}

	/**
	 * This will load a cached version of the service container, or build one from scratch.
	 */
	protected function initializeContainer()
	{
		$this->container = $this->getContainerBuilder();

		// load parameters config
		$this->registerContainerConfiguration($this->getContainerLoader($this->container));
		$this->registerServices();
	}

	/**
	 * @param ContainerInterface $container The service container
	 * @return DelegatingLoader
	 */
	public function getContainerLoader(ContainerInterface $container)
	{
		/**
		 * The FileLocator used here is one from HttpKernel, so it understands Kernel context
		 * and automatically looks for the right path.
		 */
		$locator = new FileLocator($this);
		$resolver = new LoaderResolver(array(
			new YamlFileLoader($container, $locator)
			// @todo depending on what we need, this should be expanded.
		));
		return new DelegatingLoader($resolver);
	}

	/**
	 * @todo
	 * These methods need to be present in order to answer to interface requirements.
	 * Most are only relevant when bundles are present, so we can't use them yet.
	 */
	public function boot(){}
	public function getBundle($name, $first = true){}
	public function getBundles(){}
	public function getCacheDir(){}
	public function getCharset(){}
	public function getLogDir(){}
	public function getName(){}
	public function getRootDir(){}
	public function getStartTime(){}
	public function isClassInActiveBundle($class){}
	public function isDebug(){}
	public function locateResource($name, $dir = null, $first = true){}
	public function registerBundles(){}
	public function shutdown(){}
	public function serialize($name){}
	public function unserialize($value){}
}
