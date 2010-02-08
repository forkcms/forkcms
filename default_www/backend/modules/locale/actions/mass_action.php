<?php

/**
 * BackendLocaleMassAction
 *
 * This action is used to update one or more locale items.
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendLocaleMassAction extends BackendBaseAction
{
	/**
	 * Filter variables
	 *
	 * @var	arra
	 */
	private $filter;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// filter options
		$this->setFilter();

		// action to execute
		$action = SpoonFilter::getGetValue('action', array('delete'), 'delete');

		// no id's provided
		if(!isset($_GET['id'])) $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');

		// at least one id
		else
		{
			// redefine id's
			$aIds = (array) $_GET['id'];

			// delete comment(s)
			if($action == 'delete') BackendLocaleModel::delete($aIds);
		}

		// redirect
		$this->redirect(BackendModel::createURLForAction('index', null, null, array('language' => $this->filter['language'], 'application' => $this->filter['application'], 'module' => $this->filter['module'], 'type' => $this->filter['type'], 'name' => $this->filter['name'], 'value' => $this->filter['value'])) .'&report='. $action);
	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
	 */
	private function setFilter()
	{
		$this->filter['language'] = $this->getParameter('language');
		$this->filter['application'] = $this->getParameter('application');
		$this->filter['module'] = $this->getParameter('module');
		$this->filter['type'] = $this->getParameter('type');
		$this->filter['name'] = $this->getParameter('name');
		$this->filter['value'] = $this->getParameter('value');
	}
}

?>