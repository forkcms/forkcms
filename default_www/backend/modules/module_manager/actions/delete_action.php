<?php
/**
 * BackendModulemanagerDeleteAction
 * This is the delete-action-action
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author 		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class BackendModulemanagerDeleteAction extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');
		
		// does the item exist
		if($this->id !== null && BackendModulemanagerModel::actionExists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// delete item
			BackendModulemanagerModel::deleteAction($this->id);
		
			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') .'&report=deleted');
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}
}

?>