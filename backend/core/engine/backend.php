<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class defines the backend, it is the core. Everything starts here.
 * We create all needed instances and execute the requested action
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 */
class Backend implements ApplicationInterface
{
	/**
	 * @var BackendAction
	 */
	private $action;

	public function __construct()
	{
		$URL = new BackendURL();
		new BackendTemplate();
		new BackendNavigation();
		new BackendHeader();

		$this->action = new BackendAction();
		$this->action->setModule($URL->getModule());
		$this->action->setAction($URL->getAction());
	}

	/**
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function getResponse()
	{
		return $this->action->execute();
	}
}
