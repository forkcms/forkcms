<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

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
		BackendBlogModel::deleteSpamComments();

		// item was deleted, so redirect
		$this->redirect(BackendModel::createURLForAction('comments') . '&report=deleted-spam#tabSpam');
	}
}
