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
 */
class Backend
{
	public function __construct()
	{
		$URL = new BackendURL();
		new BackendTemplate();
		new BackendNavigation();
		new BackendHeader();

		$action = new BackendAction();
		$action->setModule($URL->getModule());
		$action->setAction($URL->getAction());
		$action->execute();
	}
}
