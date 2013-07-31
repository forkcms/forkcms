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
	 * @var array
	 */
	protected $bundleMap = array();

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
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Gets the container's base class.
	 *
	 * All names except Container must be fully qualified.
	 *
	 * @return string
	 */
	protected function getContainerBaseClass()
	{
		return 'Container';
	}

	/**
	 * Removes comments from a PHP source string.
	 *
	 * We don't use the PHP php_strip_whitespace() function
	 * as we want the content to be readable and well-formatted.
	 *
	 * @param string $source A PHP string
	 *
	 * @return string The PHP string with the comments removed
	 */
	public static function stripComments($source)
	{
		if (!function_exists('token_get_all')) {
			return $source;
		}

		$rawChunk = '';
		$output = '';
		$tokens = token_get_all($source);
		for (reset($tokens); false !== $token = current($tokens); next($tokens)) {
			if (is_string($token)) {
				$rawChunk .= $token;
			} elseif (T_START_HEREDOC === $token[0]) {
				$output .= preg_replace(array('/\s+$/Sm', '/\n+/S'), "\n", $rawChunk).$token[1];
				do {
					$token = next($tokens);
					$output .= $token[1];
				} while ($token[0] !== T_END_HEREDOC);
				$rawChunk = '';
			} elseif (!in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
				$rawChunk .= $token[1];
			}
		}

		// replace multiple new lines with a single newline
		$output .= preg_replace(array('/\s+$/Sm', '/\n+/S'), "\n", $rawChunk);

		return $output;
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
		// This is also where symfony loads and stores the names of the active bundles

		/*
		 * Debug status and environment are params of the Kernel constructor, and
		 * are set via a separate front controller.
		 *
		 * Fork sets them directly in /app/config/parameters.yml through the installer.
		 * We can add additional non-installer related configuration options here.
		 */
		return array(
			//'kernel.debug' => $this->debug,
			//'kernel.environment' => $this->environment,
			'kernel.root_dir' => $this->getRootDir(),
		);
	}

	/**
	 * Gets the container class.
	 *
	 * @return string The container class
	 */
	protected function getContainerClass()
	{
		return $this->name.ucfirst($this->environment).($this->debug ? 'Debug' : '').'ProjectContainer';
	}

	/**
	 * This will load a cached version of the service container, or build one from scratch.
	 */
	protected function initializeContainer()
	{
		$class = $this->getContainerClass();

		$cache = new ConfigCache($this->getCacheDir().$class.'.php', true);
		if (!$cache->isFresh()) {
			$container = $this->buildContainer();
			$container->compile();
			$this->dumpContainer($cache, $container, $class, $this->getContainerBaseClass());
		}

		require_once $cache;

		$this->container = new $class();
		$this->container->set('kernel', $this);
	}

	/**
	 * @param ContainerInterface $container The service container
	 * @return DelegatingLoader
	 */
	public function getContainerLoader(ContainerInterface $container)
	{
		/*
		 * The FileLocator used here is one from HttpKernel, so it understands Kernel context
		 * and automatically looks for the right path.
		 */
		$locator = new FileLocator($this);
		$resolver = new LoaderResolver(array(
			new XmlFileLoader($container, $locator),
			new YamlFileLoader($container, $locator),
			new IniFileLoader($container, $locator),
			new PhpFileLoader($container, $locator),
			new ClosureLoader($container),
		));
		return new DelegatingLoader($resolver);
	}

	/**
	 * Initializes the data structures related to the bundle management.
	 *
	 *  - the bundles property maps a bundle name to the bundle instance,
	 *  - the bundleMap property maps a bundle name to the bundle inheritance hierarchy (most derived bundle first).
	 *
	 * @throws \LogicException if two bundles share a common name
	 * @throws \LogicException if a bundle tries to extend a non-registered bundle
	 * @throws \LogicException if a bundle tries to extend itself
	 * @throws \LogicException if two bundles extend the same ancestor
	 */
	protected function initializeBundles()
	{
		// init bundles
		$this->bundles = array();
		$topMostBundles = array();
		$directChildren = array();

		foreach ($this->registerBundles() as $bundle) {
			$name = $bundle->getName();
			if (isset($this->bundles[$name])) {
				throw new \LogicException(sprintf('Trying to register two bundles with the same name "%s"', $name));
			}
			$this->bundles[$name] = $bundle;

			if ($parentName = $bundle->getParent()) {
				if (isset($directChildren[$parentName])) {
					throw new \LogicException(sprintf('Bundle "%s" is directly extended by two bundles "%s" and "%s".', $parentName, $name, $directChildren[$parentName]));
				}
				if ($parentName == $name) {
					throw new \LogicException(sprintf('Bundle "%s" can not extend itself.', $name));
				}
				$directChildren[$parentName] = $name;
			} else {
				$topMostBundles[$name] = $bundle;
			}
		}

		// look for orphans
		if (count($diff = array_values(array_diff(array_keys($directChildren), array_keys($this->bundles))))) {
			throw new \LogicException(sprintf('Bundle "%s" extends bundle "%s", which is not registered.', $directChildren[$diff[0]], $diff[0]));
		}

		// inheritance
		foreach ($topMostBundles as $name => $bundle) {
			$bundleMap = array($bundle);
			$hierarchy = array($name);

			while (isset($directChildren[$name])) {
				$name = $directChildren[$name];
				array_unshift($bundleMap, $this->bundles[$name]);
				$hierarchy[] = $name;
			}

			foreach ($hierarchy as $bundle) {
				$this->bundleMap[$bundle] = $bundleMap;
				array_pop($bundleMap);
			}
		}

	}

	/**
	 * Boot the Kernel
	 */
	public function boot()
	{
		if (true === $this->booted) {
			return;
		}

		$this->initializeBundles();
		$this->initializeContainer();

		foreach ($this->getBundles() as $bundle) {
			$bundle->setContainer($this->container);
			$bundle->boot();
		}

		$this->booted = true;
	}

	/**
	 * Return all the available bundles.
	 *
	 * @return array
	 */
	public function getBundles()
	{
		return $this->bundles;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @api
	 */
	public function getBundle($name, $first = true)
	{
		if (!isset($this->bundleMap[$name])) {
			throw new \InvalidArgumentException(sprintf('Bundle "%s" does not exist or it is not enabled. Maybe you forgot to add it in the registerBundles() method of your %s.php file?', $name, get_class($this)));
		}

		if (true === $first) {
			return $this->bundleMap[$name][0];
		}

		return $this->bundleMap[$name];
	}

	/**
	 * Builds the service container.
	 *
	 * @return ContainerBuilder The compiled service container
	 *
	 * @throws \RuntimeException
	 */
	protected function buildContainer()
	{
		foreach (array('cache' => $this->getCacheDir(), 'logs' => $this->getLogDir()) as $name => $dir) {
			if (!is_dir($dir)) {
				if (false === @mkdir($dir, 0777, true)) {
					throw new \RuntimeException(sprintf("Unable to create the %s directory (%s)\n", $name, $dir));
				}
			} elseif (!is_writable($dir)) {
				throw new \RuntimeException(sprintf("Unable to write in the %s directory (%s)\n", $name, $dir));
			}
		}

		$container = $this->getContainerBuilder();
		$container->addObjectResource($this);
		$this->prepareContainer($container);

		if (null !== $cont = $this->registerContainerConfiguration($this->getContainerLoader($container))) {
			$container->merge($cont);
		}

		return $container;
	}

	/**
	 * Dumps the service container to PHP code in the cache.
	 *
	 * @param ConfigCache	  $cache	 The config cache
	 * @param ContainerBuilder $container The service container
	 * @param string		   $class	 The name of the class to generate
	 * @param string		   $baseClass The name of the container's base class
	 */
	protected function dumpContainer(ConfigCache $cache, ContainerBuilder $container, $class, $baseClass)
	{
		// cache the container
		$dumper = new PhpDumper($container);
		$content = $dumper->dump(array('class' => $class, 'base_class' => $baseClass));
		if (!$this->debug) {
			$content = self::stripComments($content);
		}

		$cache->write($content, $container->getResources());
	}

	/**
	 * Prepares the ContainerBuilder before it is compiled.
	 *
	 * @param ContainerBuilder $container A ContainerBuilder instance
	 */
	protected function prepareContainer(ContainerBuilder $container)
	{
		$extensions = array();
		foreach ($this->bundles as $bundle) {
			if ($extension = $bundle->getContainerExtension()) {
				$container->registerExtension($extension);
				$extensions[] = $extension->getAlias();
			}

			if ($this->debug) {
				$container->addObjectResource($bundle);
			}
		}
		foreach ($this->bundles as $bundle) {
			$bundle->build($container);
		}

		// ensure these extensions are implicitly loaded
		$container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass($extensions));
	}

	/**
	 * {@inheritdoc}
	 *
	 * @api
	 */
	public function getCacheDir()
	{
		return $this->rootDir.'/cache/'.$this->environment;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @api
	 */
	public function getLogDir()
	{
		return $this->rootDir.'/logs';
	}

	/**
	 * {@inheritdoc}
	 *
	 * @api
	 */
	public function getRootDir()
	{
		if (null === $this->rootDir) {
			$this->rootDir = rtrim(__DIR__, '/');
		}

		return $this->rootDir;
	}

	/**
	 * @var ApplicationRouting
	 */
	private $router;

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
	 * @param Request $request
	 * @param int[optional] $type
	 * @param bool[optional] $catch
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
	{
		$this->router = new ApplicationRouting($request, $this);
		return $this->router->handleRequest();
	}

	/**
	 * @todo
	 * These methods need to be present in order to answer to interface requirements.
	 * Most are only relevant when bundles are present, so we can't use them yet.
	 */
	public function getCharset(){}
	public function getName(){}
	public function getStartTime(){}
	public function isClassInActiveBundle($class){}
	public function isDebug(){}
	public function locateResource($name, $dir = null, $first = true){}
	public function shutdown(){}
	public function serialize($name){}
	public function unserialize($value){}
}
