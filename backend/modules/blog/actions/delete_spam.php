<?php

/**
 * This action will delete a blogpost
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@verkoyen.eu>
 * @since		2.0
 */
class BackendBlogDeleteSpam extends BackendBaseActionDelete
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

		// delete item
		BackendBlogModel::deleteSpamComments($this->id);

		// item was deleted, so redirect
		$this->redirect(BackendModel::createURLForAction('comments') . '&report=deleted-spam#tabSpam');
	}
}

?>