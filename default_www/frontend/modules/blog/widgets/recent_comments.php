<?php

/**
 * This is a widget with recent comments on all blog-articles
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBlogWidgetRecentComments extends FrontendBaseWidget
{
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

		// parse
		$this->parse();
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign comments
		$this->tpl->assign('widgetBlogRecentComments', FrontendBlogModel::getRecentComments(5));
	}
}

?>