<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action will refresh the campaign monitor access token
 *
 * @author Lowie Benoot <lowie.benoot@wijs.be>
 */
class BackendMailmotorRefreshToken extends BackendBaseAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// refresh token
		$result = BackendMailmotorCMHelper::refreshAccessToken(true);

		// init redirect url
		$redirectURL = BackendModel::createURLForAction('index');

		// error? add error message
		if(isset($result['error']))
		{
			$redirectURL .= '&error=refresh-token-failed&var=' . urlencode($result['error']);
		}
		// no error, add success message
		else
		{
			$redirectURL .= '&report=token-refreshed';
		}

		$this->redirect($redirectURL);
	}
}
