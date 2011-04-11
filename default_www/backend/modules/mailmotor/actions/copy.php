<?php

/**
 * This action is used to create a draft based on an existing mailing.
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorCopy extends BackendBaseAction
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
		if(empty($id) || !BackendMailmotorModel::existsMailing($id)) $this->redirect(BackendModel::createURLForAction('index') . '&error=mailing-does-not-exist');

		// at least one id
		else
		{
			// get the mailing and reset some fields
			$mailing = BackendMailmotorModel::getMailing($id);
			$mailing['name'] = $mailing['name'] . ' (#' . (BackendMailmotorModel::getMaximumId() + 1) . ')';
			$mailing['status'] = 'concept';
			$mailing['send_on'] = null;
			$mailing['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
			$mailing['edited_on'] = $mailing['created_on'];
			$mailing['data'] = serialize($mailing['data']);
			unset($mailing['recipients'], $mailing['id'], $mailing['cm_id'], $mailing['send_on_raw']);

			// set groups
			$groups = $mailing['groups'];
			unset($mailing['groups']);

			// create a new mailing based on the old one
			$newId = BackendMailmotorModel::insertMailing($mailing);

			// update groups for this mailing
			BackendMailmotorModel::updateGroupsForMailing($newId, $groups);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('index') . '&report=mailing-copied&var=' . $mailing['name']);
	}
}

?>