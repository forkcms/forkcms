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
 * This class defines the frontend, it is the core. Everything starts here.
 * We create all needed instances.
 *
 * @todo make this an interface implementation.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class Frontend implements ContainerAwareInterface
{
	/**
	 * The service container
	 *
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @var FrontendPage
	 */
	private $page;

	/**
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function display()
	{
		return $this->page->display();
	}

	/**
	 * Initializes the entire frontend; prelaod FB, URL, template and the requested page.
	 */
	public function initialize()
	{
		/*
		 * @todo
		 * In the long run models should not be a collection of static methods.
		 * This should be considered temporary until that time comes.
		 */
		FrontendModel::setContainer($this->container);

		$this->initializeFacebook();
		new FrontendURL();
		new FrontendTemplate();

		/*
		 * At this point we will load the rest of the page.
		 * This method exists because the service container needs to be set before
		 * the page's functionality gets loaded.
		 */
		$this->page = new FrontendPage();
		$this->page->setContainer($this->container);
		$this->page->load();
	}

	/**
	 * Initialize Facebook
	 */
	private function initializeFacebook()
	{
		// get settings
		$facebookApplicationId = FrontendModel::getModuleSetting('core', 'facebook_app_id');
		$facebookApplicationSecret = FrontendModel::getModuleSetting('core', 'facebook_app_secret');

		// needed data available?
		if($facebookApplicationId != '' && $facebookApplicationSecret != '')
		{
			// require
			require_once 'external/facebook.php';

			// create instance
			$facebook = new Facebook($facebookApplicationSecret, $facebookApplicationId);

			// get the cookie, this will set the access token.
			$facebook->getCookie();

			// store in reference
			Spoon::set('facebook', $facebook);

			// trigger event
			FrontendModel::triggerEvent('core', 'after_facebook_initialization');
		}
	}

	/**
	 * @param ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
}
