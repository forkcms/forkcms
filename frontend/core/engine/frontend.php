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
 */
class Frontend implements ApplicationInterface
{
	/**
	 * @var FrontendPage
	 */
	private $page;

	public function __construct()
	{
		$this->initializeFacebook();

		new FrontendURL();
		new FrontendTemplate();
		$this->page = new FrontendPage();
	}

	/**
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function getResponse()
	{
		return $this->page->display();
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
}
