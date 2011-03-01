<?php

/**
 * This is the index-action
 *
 * @package		frontend
 * @subpackage	tags
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class FrontendTagsIndex extends FrontendBaseBlock
{
	/**
	 * List of tags
	 *
	 * @var	array
	 */
	private $tags = array();


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// load template
		$this->loadTemplate();

		// load the data
		$this->getData();

		// parse
		$this->parse();
	}


	/**
	 * Load the data from the database.
	 *
	 * @return	void
	 */
	private function getData()
	{
		$this->tags = FrontendTagsModel::getAll();
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// make tags available
		$this->tpl->assign('tags', $this->tags);
	}
}

?>