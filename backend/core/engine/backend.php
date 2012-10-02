<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class defines the backend, it is the core. Everything starts here.
 * We create all needed instances and execute the requested action
 *
 * @todo make this an interface implementation.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class Backend implements ContainerAwareInterface
{
	/**
	 * @var BackendAction
	 */
	private $action;

	/**
	 * The service container
	 *
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function display()
	{
		return $this->action->execute();
	}

	public function initialize()
	{
		BackendModel::setContainer($this->container);

		$URL = new BackendURL();
		new BackendTemplate();
		new BackendNavigation();
		new BackendHeader();

		$this->action = new BackendAction();
		$this->action->setModule($URL->getModule());
		$this->action->setAction($URL->getAction());
	}

	/**
	 * @param ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
}
