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
	 * This will disappear in time in favour of container-driven parameters.
	 * @deprecated
	 */
	protected function defineForkConstants()
	{
		$container = $this->getContainer();

		Spoon::setDebug($container->getParameter('fork.debug'));
		Spoon::setDebugMessage($container->getParameter('fork.debug_email'));
		Spoon::setDebugMessage($container->getParameter('fork.debug_message'));
		Spoon::setCharset($container->getParameter('fork.charset'));

		/**
		 * @deprecated SPOON_* constants are deprecated in favor of Spoon::set*().
		 * Will be removed in the next major release.
		 */
		if(!defined('SPOON_DEBUG'))
		{
			define('SPOON_DEBUG', $container->getParameter('fork.debug'));
			define('SPOON_DEBUG_EMAIL', $container->getParameter('fork.debug_email'));
			define('SPOON_DEBUG_MESSAGE', $container->getParameter('fork.debug_message'));
			define('SPOON_CHARSET', $container->getParameter('fork.charset'));
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

	/**
	 * @param LoaderInterface $loader
	 */
	public function registerContainerConfiguration(LoaderInterface $loader)
	{
		// this prevents the installer from bitching that config.yml cannot load parameters.yml
		if(!file_exists(__DIR__ . '/config/parameters.yml')) return;

		// load the general config.yml
		$loader->load(__DIR__ . '/config/config.yml');

		// define Fork constants
		$this->defineForkConstants();
	}

	/**
	 * Register our services here. This will move to bundle-level
	 * once we use the full-stack Symfony framework.
	 */
	public function registerServices()
	{
		/**
		 * @todo
		 * In symfony, the doctrine layer gets registered through app/config/config.yml.
		 * The bundles itself call it into life when needed.
		 */
		$this->getContainer()->register('database', 'SpoonDatabase')
			->addArgument('%database.driver%')
			->addArgument('%database.host%')
			->addArgument('%database.user%')
			->addArgument('%database.password%')
			->addArgument('%database.name%')
			->addArgument('%database.port%')
			->addMethodCall(
				'execute',
				array(
					'SET CHARACTER SET :charset, NAMES :charset, time_zone = "+0:00"',
					array('charset' => 'utf8')
				)
			);
	}
}

