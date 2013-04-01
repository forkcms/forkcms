<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The base model's responsibility is to provide the service container to
 * both FrontendModel and BackendModel.
 *
 * In the long run models should not be a collection of static methods, and this will disappear.
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
	 * @return ContainerInterface
	 */
	public static function getContainer()
	{
		return self::$container;
	}

	/**
	 * @param ContainerInterface[optional] $container
	 */
	public static function setContainer(ContainerInterface $container = null)
	{
		self::$container = $container;
	}
}

