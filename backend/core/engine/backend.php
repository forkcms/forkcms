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
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class Backend extends KernelLoader implements ApplicationInterface
{
	/**
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function display()
	{
		return $this->action->execute();
	}

	public function initialize()
	{
		/*
		 * @todo
		 * In the long run models should not be a collection of static methods.
		 * This should be considered temporary until that time comes.
		 */
		BackendModel::setContainer($this->getKernel()->getContainer());

		$URL = new BackendURL();
		new BackendTemplate();
		new BackendNavigation();
		new BackendHeader();

		$this->action = new BackendAction();
		$this->action->setModule($URL->getModule());
		$this->action->setAction($URL->getAction());
		$this->action->setKernel($this->getKernel());
	}
}
