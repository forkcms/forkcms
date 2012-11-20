<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class defines the frontend, it is the core. Everything starts here.
 * We create all needed instances.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jelmer Snoeck <jelmer@siphoc.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class Frontend extends KernelLoader implements ApplicationInterface
{
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
	 * Initializes the entire frontend; preload FB, URL, template and the requested page.
	 *
	 * This method exists because the service container needs to be set before
	 * the page's functionality gets loaded.
	 */
	public function initialize()
	{
		$this->initializeFacebook();
		new FrontendURL();
		new FrontendTemplate();

		// Load the rest of the page.
		$this->page = new FrontendPage();
		$this->page->setKernel($this->getKernel());
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
			$config = array(
				'appId' => $facebookApplicationId,
				'secret' => $facebookApplicationSecret,
			);

			// create instance
			$facebook = new Facebook($config);

			// grab the signed request, if a user is logged in the access token will be set
			$facebook->getSignedRequest();

			// store in reference
			Spoon::set('facebook', $facebook);

			// trigger event
			FrontendModel::triggerEvent('core', 'after_facebook_initialization');
		}
	}
}
