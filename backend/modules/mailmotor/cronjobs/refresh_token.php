<?php

require_once BACKEND_MODULES_PATH . '/mailmotor/engine/helper.php';

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This cronjob will refresh the campaign monitor access token.
 *
 * @author Lowie Benoot <lowie.benoot@wijs.be>
 */
class BackendMailmotorCronjobRefreshToken extends BackendBaseCronjob
{
	public function execute()
	{
		parent::execute();
		BackendMailmotorCMHelper::refreshAccessToken();
	}
}
