<?php

use Symfony\Component\DependencyInjection\ContainerInterface;

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
		$write = (bool) $write;

		// Check if we already stored the database in the container
		if(self::$container->has('database') === false)
		{
			self::$container->setParameter('database.type', DB_TYPE);
			self::$container->setParameter('database.hostname', DB_HOSTNAME);
			self::$container->setParameter('database.port', DB_PORT);
			self::$container->setParameter('database.username', DB_USERNAME);
			self::$container->setParameter('database.password', DB_PASSWORD);
			self::$container->setParameter('database.database', DB_DATABASE);

			self::$container->register('database', 'SpoonDatabase')
							->addArgument('%database.type%')
							->addArgument('%database.hostname%')
							->addArgument('%database.username%')
							->addArgument('%database.password%')
							->addArgument('%database.database%')
							->addArgument('%database.port%');

			self::$container->get('database')->execute(
				'SET CHARACTER SET utf8, NAMES utf8, time_zone = "+0:00"'
			);
		}

		return self::$container->get('database');
	}

	public static function setContainer(ContainerInterface $container = null)
	{
		self::$container = $container;
	}
}
