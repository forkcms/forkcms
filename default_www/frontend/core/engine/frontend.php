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
		$facebookApiKey = FrontendModel::getModuleSetting('core', 'facebook_api_key');

		// needed data available?
		if($facebookApplicationId != '' && $facebookApplicationSecret != '' && $facebookApiKey != '')
		{
			// require
			require_once 'external/facebook.php';

			// create instance
			$facebook = new Facebook($facebookApiKey, $facebookApplicationSecret, $facebookApplicationId);

			// get the cookie
			$data = $facebook->getCookie();

			// set the token if available
			if(isset($data['access_token'])) $facebook->setToken($data['access_token']);

			// store in reference
			Spoon::set('facebook', $facebook);
		}
	}
}

?>