<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\AddClassesToCachePass;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;

/**
 * The Kernel provides a proper way to load an environment and DI container.
 * It also handles requests and responses.
 *
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
abstract class Kernel extends SymfonyKernel
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
	 * Is the kernel booted?
	 *
	 * @var boolean
	 */
	protected $booted = false;

	/**
	 * All the available bundles.
	 *
	 * @var array
	 */
	protected $bundles = array();

	/**
	 * Set the root dir for our project.
	 *
	 * @var string
	 */
	protected $rootDir;

	/**
	 * The name of our application. We'll hardcode this to Fork for now.
	 *
	 * @var string
	 */
	protected $name = 'ForkCMS';

	/**
	 * To mirror symfony, $environment should not be optional, but for now we have no reason
	 * to actually do this because we can't use the profiler.
	 *
	 * Debugging is added to mirror Symfony, but does not actually do anything at this moment.
	 *
	 * @param string[optional] $environment
	 * @param bool[optional] $debug
	 */
	public function __construct($environment = null, $debug = false)
	{
		$this->environment = $environment;
		$this->debug = $debug;
		$this->rootDir = $this->getRootDir();

		if (file_exists(__DIR__ . '/config/parameters.yml')) {
			$this->boot();

			// define Fork constants
			$this->defineForkConstants();
		}
	}

	/**
	 * This will disappear in time in favour of container-driven parameters.
	 * @deprecated
	 */
	protected function defineForkConstants()
	{
		$container = $this->getContainer();

		Spoon::setDebug($container->getParameter('kernel.debug'));
		Spoon::setDebugMessage($container->getParameter('fork.debug_email'));
		Spoon::setDebugMessage($container->getParameter('fork.debug_message'));
		Spoon::setCharset($container->getParameter('kernel.charset'));

		/**
		 * @deprecated SPOON_* constants are deprecated in favor of Spoon::set*().
		 * Will be removed in the next major release.
		 */
		if(!defined('SPOON_DEBUG'))
		{
			define('SPOON_DEBUG', $container->getParameter('kernel.debug'));
			define('SPOON_DEBUG_EMAIL', $container->getParameter('fork.debug_email'));
			define('SPOON_DEBUG_MESSAGE', $container->getParameter('fork.debug_message'));
			define('SPOON_CHARSET', $container->getParameter('kernel.charset'));
		}

		if(!defined('PATH_WWW'))
		{
			define('PATH_WWW', $container->getParameter('site.path_www'));
			define('PATH_LIBRARY', $container->getParameter('site.path_library'));
		}

		define('SITE_DEFAULT_LANGUAGE', $container->getParameter('site.default_language'));
		define('SITE_DEFAULT_TITLE', $container->getParameter('site.default_title'));
		define('SITE_MULTILANGUAGE', $container->getParameter('site.multilanguage'));
		define('SITE_DOMAIN', $container->getParameter('site.domain'));
		define('SITE_PROTOCOL', $container->getParameter('site.protocol'));
		define('SITE_URL', SITE_PROTOCOL . '://' . SITE_DOMAIN);

		define('FORK_VERSION', $container->getParameter('fork.version'));

		define('ACTION_GROUP_TAG', $container->getParameter('action.group_tag'));
		define('ACTION_RIGHTS_LEVEL', $container->getParameter('action.rights_level'));
	}

	/**
	 * Handles a request to convert into a response.
	 * When $catch is true, the implementation must catch all exceptions
	 * and do its best to convert them to a Response instance.
	 *
	 * We intercept this object so we can load all functionality involved with Fork.
	 *
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
	{
		$this->router = new ApplicationRouting($request, $this);

		return $this->router->handleRequest();
	}
}
