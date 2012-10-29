<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The base model's responsability is to provide getDB() functionality to
 * both FrontendModel and BackendModel.
 *
 * This class exists for the sole purpose of not breaking the current models implementation.
 * In the long run models should not be a collection of static methods.
 *
 * @author Dave Lens <dave.lens@wijs.be>
 */
class BaseModel
{
	/**
	 * @var Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private static $container;

	/**
	 * @param bool[optional] $write
	 * @deprecated
	 */
	public static function getDB($write = false)
	{
		// If the container is not set at this point it probably means we're in the Fork installer
		if(self::$container === null)
		{
			// Make sure we don't create multiple connections in the installation process
			if(!Spoon::exists('database'))
			{
				require_once PATH_LIBRARY . '/globals.php';
				Spoon::set('database', new SpoonDatabase(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT));
			}

			return Spoon::get('database');
		}

		return self::$container->get('database');
	}

	public static function setContainer(ContainerInterface $container = null)
	{
		self::$container = $container;
	}
}
