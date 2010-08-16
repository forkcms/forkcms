<?php

/**
 * BackendCoreAPI
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
	 * Get the API-key for a user
	 *
	 * @return	array
	 * @param	array $args		The parameters provided.
	 */
	public static function getAPIKey($arguments)
	{
		// get variables
		$email = SpoonFilter::getValue($arguments['email'], null, '');
		$password = SpoonFilter::getValue($arguments['password'], null, '');

		// validate
		if($email == '') API::output(API::BAD_REQUEST, 'No email-parameter provided.');
		if($password == '') API::output(API::BAD_REQUEST, 'No password-parameter provided.');

		// load user
		$user = new BackendUser(null, $email);

		// does the user have access?
		if($user->getSetting('api_access', false) == false) API::output(API::FORBIDDEN, 'Uses isn\'t allowed to use the API.');

		// create the key if needed
		if($user->getSetting('api_key', null) == null) $user->setSetting('api_key', uniqid());

		// return the key
		return array('api_key' => $user->getSetting('api_key'));
	}
}

?>