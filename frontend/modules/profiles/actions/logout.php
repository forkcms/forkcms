<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the logout-action.
 *
 * @author Lester Lievens <lester@netlash.com>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class FrontendProfilesLogout extends FrontendBaseBlock
{
	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		// logout
		if(FrontendProfilesAuthentication::isLoggedIn()) FrontendProfilesAuthentication::logout();

		// trigger event
		FrontendModel::triggerEvent('profiles', 'after_logout');

		// redirect
		$this->redirect(SITE_URL);
	}
}
