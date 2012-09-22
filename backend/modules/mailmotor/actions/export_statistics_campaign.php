<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action is used to export statistics for a given campaign ID
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorExportStatisticsCampaign extends BackendBaseAction
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
		if(!BackendMailmotorModel::existsCampaign($id)) $this->redirect(BackendModel::createURLForAction('campaigns') . '&error=campaign-does-not-exist');

		// at least one id
		else BackendMailmotorModel::exportStatisticsByCampaignID($id);

		// redirect
		$this->redirect(BackendModel::createURLForAction('groups') . '&report=export-failed');
	}
}
