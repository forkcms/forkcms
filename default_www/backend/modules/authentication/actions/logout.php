<?php

/**
 * This is the logout-action, it will logout the current user
 *
 * @package		backend
 * @subpackage	authentication
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendAuthenticationLogout extends BackendBaseAction
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// log out the current user
		BackendAuthentication::logout();

		// redirect to login-screen
		$this->redirect(BackendModel::createUrlForAction('index', 'authentication'));
	}
}

?>