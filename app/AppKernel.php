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
		// this prevents the installer from bitching that config.yml cannot load parameters.yml
		if(!file_exists(__DIR__ . '/config/parameters.yml')) return;

		// load the general config.yml
		$loader->load(__DIR__ . '/config/config.yml');
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

