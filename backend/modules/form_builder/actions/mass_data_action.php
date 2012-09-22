<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This action is used to update one or more data items
 *
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
 */
class BackendFormBuilderMassDataAction extends BackendBaseAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// action to execute
		$action = SpoonFilter::getGetValue('action', array('delete'), '');

		// form id
		$formId = SpoonFilter::getGetValue('form_id', null, '', 'int');

		// no id's provided
		if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('index') . '&error=no-items-selected');

		// no action provided
		elseif($action == '') $this->redirect(BackendModel::createURLForAction('index') . '&error=no-action-selected');

		// valid form id
		elseif(!BackendFormBuilderModel::exists($formId)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

		// at least one id
		else
		{
			// redefine id's
			$ids = (array) $_GET['id'];

			// delete comment(s)
			if($action == 'delete') BackendFormBuilderModel::deleteData($ids);

			// define report
			$report = (count($ids) > 1) ? 'items-' : 'item-';

			// init var
			if($action == 'delete') $report .= 'deleted';

			// redirect
			$this->redirect(BackendModel::createURLForAction('data') . '&id=' . $formId . '&report=' . $report);
		}
	}
}
