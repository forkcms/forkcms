<?php

/**
 * PagesIndex
 *
 * This is the index-action (default), it will display the tree, all other actions will be ajax-based
 *
 * @package		backend
 * @subpackage	pages
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class PagesIndex extends BackendBaseActionIndex
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

		// load the form
		$this->loadForm();

		// display the page
		$this->display();
	}

	private function loadForm()
	{

	}

}
?>