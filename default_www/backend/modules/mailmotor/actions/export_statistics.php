<?php

/**
 * This action is used to export statistics by mailing ID
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorExportStatistics extends BackendBaseAction
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
		if(!BackendMailmotorModel::existsMailing($id)) $this->redirect(BackendModel::createURLForAction('index') . '&error=mailing-does-not-exist');

		// at least one id
		else BackendMailmotorModel::exportStatistics($id);

		// redirect
		$this->redirect(BackendModel::createURLForAction('groups') . '&report=export-failed');
	}
}

?>