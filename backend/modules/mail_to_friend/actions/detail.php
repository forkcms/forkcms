<?php

/**
 * This is the Detail action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	mail_to_friend
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		2.6.10
 */
class BackendMailToFriendDetail extends BackendBaseActionEdit
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

		// load the data
		$this->loadData();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the data
	 *
	 * @return	void
	 */
	protected function loadData()
	{
		// get the id
		$this->id = $this->getParameter('id', 'int', null);

		// if there is no id given, redirect
		if($this->id == null) $this->redirect(BackendModel::createURLForAction('index') . '&amp;error=non-existing');

		// does the item exist?
		if(!BackendMailToFriendModel::exists($this->id)) $this->redirect(BackendModel::createURLForAction('index') . '&amp;error=non-existing');

		// get the item
		$this->record = BackendMailToFriendModel::get($this->id);
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// parse the record
		$this->tpl->assign('item', $this->record);
	}
}
