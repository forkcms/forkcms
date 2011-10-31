<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the Detail action, it will display a form to create a new item
 *
 * @author Jelmer Snoeck <jelmer@netlash.com>
 */
class BackendMailToFriendDetail extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadData();

		$this->parse();
		$this->display();
	}

	/**
	 * Load the data
	 */
	protected function loadData()
	{
		$this->id = $this->getParameter('id', 'int', null);

		// valid id checks
		if($this->id == null) $this->redirect(BackendModel::createURLForAction('index') . '&amp;error=non-existing');
		if(!BackendMailToFriendModel::exists($this->id)) $this->redirect(BackendModel::createURLForAction('index') . '&amp;error=non-existing');

		$this->record = BackendMailToFriendModel::get($this->id);
	}

	/**
	 * Parse the data
	 */
	protected function parse()
	{
		$this->tpl->assign('item', $this->record);
	}
}
