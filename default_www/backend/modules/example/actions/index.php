<?php

/**
 * BackendExampleIndex
 * This is the index-action (default), it will display the example page.
 *
 * @package		backend
 * @subpackage	example
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendExampleIndex extends BackendBaseActionAdd
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

		$frm = new BackendForm('test');
		$frm->addEditor('editor');
		$frm->addEditor('editor2');
		$frm->parse($this->tpl);

		// display the page
		$this->display();
	}
}

?>