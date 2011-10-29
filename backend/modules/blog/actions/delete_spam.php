<?php

/**
 * This action will delete a blogpost
 *
 * @author Tijs Verkoyen <tijs@verkoyen.eu>
 */
class BackendBlogDeleteSpam extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		BackendBlogModel::deleteSpamComments($this->id);

		// item was deleted, so redirect
		$this->redirect(BackendModel::createURLForAction('comments') . '&report=deleted-spam#tabSpam');
	}
}
