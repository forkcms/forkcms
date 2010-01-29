<?php

/**
 * FrontendBlogWidgetRecentComments
 *
 * This is a widget with recent comments on all blog-articles
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBlogWidgetRecentComments extends FrontendBaseWidget
{
	/**
	 * The recent comments
	 *
	 * @var	array
	 */
	private $recentComments;


	/**
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// load the data
		$this->getData();

		// parse
		$this->parse();
	}


	/**
	 * Load the data
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get recent comments
		$this->recentComments = FrontendBlogModel::getRecentComments(5);
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign comments
		$this->tpl->assign('recentComments', $this->recentComments);
	}
}
?>