<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action is used to export email addresses by group ID
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorExportAddresses extends BackendBaseAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// action to execute
		$id = SpoonFilter::getGetValue('id', null, 0);

		// no id's provided
		if(empty($id)) $this->redirect(BackendModel::createURLForAction('groups') . '&error=no-items-selected');

		// at least one id
		else
		{
			// export all addresses
			if($id == 'all')
			{
				// fetch records
				$records = BackendMailmotorModel::getAddresses();

				// export records
				BackendMailmotorModel::exportAddresses($records);
			}

			// export addresses by group ID
			else BackendMailmotorModel::exportAddressesByGroupID($id);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('groups') . '&report=export-failed');
	}
}
