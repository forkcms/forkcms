<?php

/**
 * This action is used to update one or more data items
 *
 * @package		backend
 * @subpackage	form_builder
 *
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendFormBuilderMassDataAction extends BackendBaseAction
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

?>