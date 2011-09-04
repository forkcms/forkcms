<?php

/**
 * BackendModulemanagerDelete
 * This is the delete-action
 *
 * @package		backend
 * @subpackage	module_manager
 *
 * @author		Frederik Heyninck <frederik@figure8.be>
 * @since		2.0
 */
class BackendModulemanagerDelete extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->module = $this->getParameter('module', 'string');
		// does the item exist
		if($this->module !== null && BackendModulemanagerModel::exists($this->module))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// delete item
			BackendModulemanagerModel::delete($this->module);
		
			// item was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->module));
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}


}

?>