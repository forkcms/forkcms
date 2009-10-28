<?php

/**
 * SnippetsDelete
 *
 * This is the delete-action, it will delete a snippets-item
 *
 * @package		backend
 * @subpackage	snippets
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class SnippetsDelete extends BackendBaseActionDelete
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

		// does the user exists
		if(BackendSnippetsModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get all data for the user we want to edit
			$this->record = (array) BackendSnippetsModel::get($this->id);

			// delete user
			BackendSnippetsModel::delete($this->id);

			// user was deleted, so redirect
			$this->redirect(BackendModel::createURLForAction('index') .'&report=deleted&var='. urlencode($this->record['title']));
		}

		// no user found, throw an exceptions, because somebody is fucking with our url
		else $this->redirect(BackendModel::createURLForAction('index') .'&error=non-existing');
	}
}

?>