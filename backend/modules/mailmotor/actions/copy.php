<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action is used to create a draft based on an existing mailing.
 *
 * @author Dave Lens <dave.lens@netlash.com>
 */
class BackendMailmotorCopy extends BackendBaseAction
{
	/**
	 * Execute the action
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
			$mailing['status'] = 'concept';
			$mailing['send_on'] = null;
			$mailing['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s');
			$mailing['edited_on'] = $mailing['created_on'];
			unset($mailing['recipients'], $mailing['id'], $mailing['cm_id'], $mailing['send_on_raw']);

			// set groups
			$groups = $mailing['groups'];
			unset($mailing['groups']);

			// create a new mailing based on the old one
			$newId = BackendMailmotorModel::insertMailing($mailing);

			// update groups for this mailing
			BackendMailmotorModel::updateGroupsForMailing($newId, $groups);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_copy_mailing', array('item' => $mailing));
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('index') . '&report=mailing-copied&var=' . $mailing['name']);
	}
}
