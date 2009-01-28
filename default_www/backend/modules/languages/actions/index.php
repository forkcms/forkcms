<?php

/**
 * LanguagesIndex
 *
 * This is the index-action (default), it will display a datagrid depending on the given parameters
 *
 * @package		backend
 * @subpackage	languages
 *
 * @author 		Davy Hellemans<davy@netlash.com>
 * @since		2.0
 */
class LanguagesIndex extends BackendBaseActionIndex
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

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Parse the correct messages into the template
	 *
	 * @return	void
	 */
	public function parse()
	{
		// dump de datagrid met de talen!
		$datagrid = new BackendDataGridDB('SELECT * FROM languages_labels');
		$this->tpl->assign('datagrid', ($datagrid->getNumResults() > 0) ? $datagrid->getContent() : false);
	}
}

?>