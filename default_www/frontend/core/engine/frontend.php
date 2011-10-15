<?php

/**
 * This class defines the frontend, it is the core. Everything starts here.
 * We create all needed instances.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class Frontend
{
	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// initialize Facebook
		$this->initializeFacebook();

		// create URL-object
		new FrontendURL();

		// create and set template reference
		new FrontendTemplate();

		// create and set page reference
		new FrontendPage();
	}


	/**
	 * Initialize Facebook
	 *
	 * @return	void
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

?>