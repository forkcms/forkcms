<?php

/**
 * This is a widget with the blog-categories
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class FrontendBlogWidgetCategory extends FrontendBaseWidget
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
		// Limit
		$limit = FrontendModel::getModuleSetting('blog', 'overview_num_items', 10);

		// get articles
		$items = FrontendBlogModel::getAllForCategoryById($this->data['id'], $limit);
		$this->tpl->assign('widgetBlogArticlesByCategory',$items);
	}
}

?>