<?php

/**
 * In this file we store all generic functions that we will be available through the API
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendCoreAPI
{
	/**
	 * Add a device to a user.
	 *
	 * @return	void
	 * @param	string $token	The token of the device.
	 * @param	string $email	The emailaddress for the user to link the device to.
	 */
	public static function appleAdddevice($token, $email)
	{
		// authorized?
		if(API::authorize())
		{
			// redefine
			$token = str_replace(' ', '', (string) $token);

			// validate
			if($token == '') API::output(API::BAD_REQUEST, array('message' => 'No token-parameter provided.'));
			if($email == '') API::output(API::BAD_REQUEST, array('message' => 'No email-parameter provided.'));

			// we should tell the ForkAPI that we registered a device
			$publicKey = BackendModel::getModuleSetting('core', 'fork_api_public_key', '');
			$privateKey = FrontendModel::getModuleSetting('core', 'fork_api_private_key', '');

			// validate keys
			if($publicKey == '' || $privateKey == '') API::output(API::BAD_REQUEST, array('message' => 'Invalid key for the Fork API, configer them in the backend.'));

			try
			{
				// load user
				$user = new BackendUser(null, $email);

				// get current tokens
				$tokens = (array) $user->getSetting('apple_device_token');

				// not already in array?
				if(!in_array($token, $tokens)) $tokens[] = $token;

				// require the class
				require_once PATH_LIBRARY . '/external/fork_api.php';

				// create instance
				$forkAPI = new ForkAPI($publicKey, $privateKey);

				// make the call
				$forkAPI->appleRegisterDevice($token);

				// store
				if(!empty($tokens)) $user->setSetting('apple_device_token', $tokens);
			}

			// catch exceptions
			catch(Exception $e)
			{
				API::output(API::FORBIDDEN, array('message' => 'Can\'t authenticate you.'));
			}
		}
	}


	/**
	 * Remove a device from a user.
	 *
	 * @return	void
	 * @param	string $token	The token of the device.
	 * @param	string $email	The emailaddress for the user to link the device to.
	 */
	public static function appleRemovedevice($token, $email)
	{
		// authorized?
		if(API::authorize())
		{
			// redefine
			$token = str_replace(' ', '', (string) $token);

			// validate
			if($token == '') API::output(API::BAD_REQUEST, array('message' => 'No token-parameter provided.'));
			if($email == '') API::output(API::BAD_REQUEST, array('message' => 'No email-parameter provided.'));

			// we should tell the ForkAPI that we registered a device
			$publicKey = BackendModel::getModuleSetting('core', 'fork_api_public_key', '');
			$privateKey = FrontendModel::getModuleSetting('core', 'fork_api_private_key', '');

			// validate keys
			if($publicKey == '' || $privateKey == '') API::output(API::BAD_REQUEST, array('message' => 'Invalid key for the Fork API, configer them in the backend.'));

			try
			{
				// load user
				$user = new BackendUser(null, $email);

				// get current tokens
				$tokens = (array) $user->getSetting('apple_device_token');

				// not already in array?
				$index = array_search($token, $tokens);

				if($index !== false)
				{
					// remove from array
					unset($tokens[$index]);

					// save it
					$user->setSetting('apple_device_token', $tokens);
				}
			}

			// catch exceptions
			catch(Exception $e)
			{
				API::output(API::FORBIDDEN, array('message' => 'Can\'t authenticate you.'));
			}
		}
	}


	/**
	 * Get the API-key for a user.
	 *
	 * @return	array
	 * @param	string $email		The emailaddress for the user.
	 * @param	string $password	The password for the user.
	 */
	public static function getAPIKey($email, $password)
	{
		// get variables
		$email = (string) $email;
		$password = (string) $password;

		// validate
		if($email == '') API::output(API::BAD_REQUEST, array('message' => 'No email-parameter provided.'));
		if($password == '') API::output(API::BAD_REQUEST, array('message' => 'No password-parameter provided.'));

		// load user
		try
		{
			$user = new BackendUser(null, $email);
		}

		// catch exceptions
		catch(Exception $e)
		{
			API::output(API::FORBIDDEN, array('message' => 'Can\'t authenticate you.'));
		}

		// validate password
		if(!BackendAuthentication::loginUser($email, $password)) API::output(API::FORBIDDEN, array('message' => 'Can\'t authenticate you.'));

		// does the user have access?
		if($user->getSetting('api_access', false) == false) API::output(API::FORBIDDEN, array('message' => 'Your account isn\'t allowed to use the API. Contact an administrator.'));

		// create the key if needed
		if($user->getSetting('api_key', null) == null) $user->setSetting('api_key', uniqid());

		// return the key
		return array('api_key' => $user->getSetting('api_key'));
	}


	/**
	 * Get info about the site.
	 *
	 * @return	array
	 */
	public static function getInfo()
	{
		// authorized?
		if(API::authorize())
		{
			// init
			$info = array();

			// get all languages
			$languages = BackendLanguage::getActiveLanguages();
			$default = BackendModel::getModuleSetting('core', 'default_language', SITE_DEFAULT_LANGUAGE);

			// loop languages
			foreach($languages as $language)
			{
				// create array
				$var = array();

				// set attributes
				$var['language']['@attributes']['language'] = $language;
				if($language == $default) $var['language']['@attributes']['is_default'] = 'true';

				// set attributes
				$var['language']['title'] = BackendModel::getModuleSetting('core', 'site_title_' . $language);
				$var['language']['url'] = SITE_URL . '/' . $language;

				// add
				$info['languages'][] = $var;
			}

			// return info
			return $info;
		}
	}
}

?>