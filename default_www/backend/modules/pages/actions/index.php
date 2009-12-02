<?php

/**
 * PagesIndex
 *
 * This is the index-action (default), it will display the pages-overview
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

		$this->header->addJS('jquery.tree.min.js');

		// check if the cached files exists
		if(!SpoonFile::exists(PATH_WWW .'/frontend/cache/navigation/keys_'. BackendLanguage::getWorkingLanguage() .'.php')) BackendPagesModel::buildCache();
		if(!SpoonFile::exists(PATH_WWW .'/frontend/cache/navigation/navigation_'. BackendLanguage::getWorkingLanguage() .'.php')) BackendPagesModel::buildCache();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Parse the datagrid and the reports
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('tree', BackendPagesModel::getTreeHTML());
	}
}

?>