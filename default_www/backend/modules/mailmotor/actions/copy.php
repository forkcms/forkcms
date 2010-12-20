<?php

/**
 * BackendMailmotorCopy
 * This action is used to create a draft based on an existing mailing.
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author 		Dave Lens <dave@netlash.com>
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
		if(empty($id) || !BackendMailmotorModel::existsMailing($id)) $this->redirect(BackendModel::createURLForAction('index') .'&error=mailing-does-not-exist');

		// at least one id
		else
		{
			// get the mailing and reset some fields
			$item = BackendMailmotorModel::getMailing($id);
			$item['name'] = $item['name'] .' (#'. (BackendMailmotorModel::getMaximumId() + 1) .')';
			$item['status'] = 'concept';
			$item['send_on'] = null;
			$item['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
			$item['edited_on'] = $item['created_on'];
			$item['data'] = serialize($item['data']);
			unset($item['recipients'], $item['id'], $item['cm_id'], $item['send_on_raw']);

			// set groups
			$groups = $item['groups'];
			unset($item['groups']);

			// create a new mailing based on the old one
			$item['id'] = BackendMailmotorModel::insertMailing($item);

			// update groups for this mailing
			BackendMailmotorModel::updateGroupsForMailing($item['id'], $groups);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('index') .'&report=mailing-copied&var='. $item['name']);
	}
}

?>