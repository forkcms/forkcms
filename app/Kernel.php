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
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;

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
	 * Is the kernel booted?
	 *
	 * @var boolean
	 */
	protected $booted;

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

		$this->boot();
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
		// This is also where symfony loads and stores the names of the active bundles

		/**
		 * Debug status and environment are params of the Kernel constructor, and
		 * are set via a separate front controller.
		 *
		 * Fork sets them directly in /app/config/parameters.yml through the installer.
		 * We can add additional non-installer related configuration options here.
		 */
		return array(
			//'kernel.debug' => $this->debug,
			//'kernel.environment' => $this->environment,
		);
	}

	/**
	 * This will load a cached version of the service container, or build one from scratch.
	 */
	protected function initializeContainer()
	{
/*
		$this->container = $this->getContainerBuilder();
		$this->container->setParameter('kernel.log_path', __DIR__ . '/logs');

		// load parameters config
		$this->registerContainerConfiguration($this->getContainerLoader($this->container));
		$this->registerServices();

		foreach ($this->getBundles() as $bundle) {
			if ($extension = $bundle->getContainerExtension()) {
				$this->container->registerExtension($extension);
			}
		}

		foreach ($this->bundles as $bundle) {
			$bundle->build($this->container);
		}
*/

		$this->container = $this->getContainerBuilder();
		$class = 'DevProjectContainer';
        $cache = new ConfigCache($this->getCacheDir().$class.'.php', true);
        $fresh = true;
        if (!$cache->isFresh()) {
            $container = $this->buildContainer();
            $container->compile();
            $this->dumpContainer($cache, $container, $class, $this->getContainerBaseClass());

            $fresh = false;
        }

        require_once $cache;

        $this->container = new $class();
        $this->container->set('kernel', $this);

        if (!$fresh && $this->container->has('cache_warmer')) {
            $this->container->get('cache_warmer')->warmUp($this->container->getParameter('kernel.cache_dir'));
        }
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
		$this->bundleMap = array();
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

        $container->addCompilerPass(new AddClassesToCachePass($this));

        return $container;
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
            $r = new \ReflectionObject($this);
            $this->rootDir = str_replace('\\', '/', dirname($r->getFileName()));
        }

        return $this->rootDir;
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
