<?php

/**
 * This action is used to export email addresses by group ID
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorExportAddresses extends BackendBaseAction
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

?>