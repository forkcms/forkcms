<?php

/**
 * This action is used to update one or more e-mail addresses (delete, ...)
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorMassAddressAction extends BackendBaseAction
{
	/**
	 * The passed e-mails
	 *
	 * @var	array
	 */
	private $emails;


	/**
	 * The group ID we have to perform the actions for
	 *
	 * @var	int
	 */
	private $groupId;


	/**
	 * Delete addresses
	 *
	 * @return	void
	 */
	private function deleteAddresses()
	{
		// no group set
		if($this->groupId == '') $this->groupId = null;

		// get all groups
		$groupIds = BackendMailmotorModel::getGroupIDs();

		// loop the emails
		foreach($this->emails as $email)
		{
			// the group ID is not set
			if($this->groupId == null)
			{
				// if no groups were set, break here
				if(empty($groupIds)) break;

				// loop the group IDs
				foreach($groupIds as $groupId)
				{
					// try to unsubscribe this address
					try
					{
						BackendMailmotorCMHelper::unsubscribe($email, $groupId);
					}

					// ignore exceptions
					catch(Exception $e)
					{
						// do nothing
					}
				}

				// delete all addresses
				BackendMailmotorModel::deleteAddresses($email);
			}

			// group ID was set, unsubscribe the address for this group
			else BackendMailmotorCMHelper::unsubscribe($email, $this->groupId);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('addresses') . '&report=delete-addresses' . (!empty($this->groupId) ? '&group_id=' . $this->groupId : ''));
	}


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
		$action = SpoonFilter::getGetValue('action', array('delete', 'export'), '');
		$this->groupId = SpoonFilter::getGetValue('group_id', null, '');

		// no id's provided
		if(!$action) $this->redirect(BackendModel::createURLForAction('addresses') . '&error=no-action-selected');
		if(!isset($_GET['emails'])) $this->redirect(BackendModel::createURLForAction('addresses') . '&error=no-items-selected');

		// at least one id
		else
		{
			// redefine id's
			$this->emails = (array) $_GET['emails'];

			// evaluate $action, see what action was triggered
			switch($action)
			{
				case 'delete':
					$this->deleteAddresses();
				break;

				case 'export':
					$this->exportAddresses();
				break;
			}
		}
	}


	/**
	 * Export addresses
	 *
	 * @return	void
	 */
	private function exportAddresses()
	{
		// export the addresses
		BackendMailmotorModel::exportAddresses($this->emails);
	}
}

?>