<?php

/**
 * This is the logout-action.
 *
 * @package		frontend
 * @subpackage	profiles
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FrontendProfilesLogout extends FrontendBaseBlock
{
	/**
	 * Execute the extra.
	 *
	 * @return	void
	 */
	public function execute()
	{
		// logout
		if(FrontendProfilesAuthentication::isLoggedIn()) FrontendProfilesAuthentication::logout();

		// redirect
		$this->redirect(SITE_URL);
	}
}

?>