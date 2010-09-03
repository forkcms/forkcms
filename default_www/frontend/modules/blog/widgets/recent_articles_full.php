<?php

/**
 * FrontendBlogWidgetRecentArticlesFull
 * This is a widget with recent blog-articles
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBlogWidgetRecentArticlesFull extends FrontendBaseWidget
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
		$this->tpl->assign('widgetBlogRecentArticlesFull', FrontendBlogModel::getAll(FrontendModel::getModuleSetting('blog', 'recent_articles_full_num_items', 5)));
	}
}

?>