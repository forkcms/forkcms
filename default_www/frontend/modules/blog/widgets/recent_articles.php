<?php

/**
 * FrontendBlogWidgetRecentArticles
 * This is a widget with recent blog-articles
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBlogWidgetRecentArticles extends FrontendBaseWidget
{
	/**
	 * The recent articles
	 * @var	array
	 */
	private $recentArticles;


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
		$this->recentArticles = FrontendBlogModel::getAll(FrontendModel::getModuleSetting('blog', 'recent_articles_number_of_items', 5));
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	private function parse()
	{
		// assign comments
		$this->tpl->assign('blogRecentArticles', $this->recentArticles);
	}
}

?>