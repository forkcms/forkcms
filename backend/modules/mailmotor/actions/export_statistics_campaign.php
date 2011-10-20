<?php

/**
 * This action is used to export statistics for a given campaign ID
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorExportStatisticsCampaign extends BackendBaseAction
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

?>