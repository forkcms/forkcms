<?php

/**
 * This is the index-action
 *
 * @package		frontend
 * @subpackage	location
 *
 * @author		Matthias Mullie <matthias@netlash.com>
 * @since		2.1
 */
class FrontendLocationIndex extends FrontendBaseBlock
{
	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// parse
		$this->parse();
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// show message
		$this->tpl->assign('locationItems', FrontendLocationModel::getAll());

		// hide form
		$this->tpl->assign('locationSettings', FrontendModel::getModuleSettings('location'));
	}
}

?>