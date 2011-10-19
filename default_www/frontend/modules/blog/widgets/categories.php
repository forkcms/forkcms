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
class FrontendBlogWidgetCategories extends FrontendBaseWidget
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
		// get categories
		$categories = FrontendBlogModel::getAllCategories();

		// any categories?
		if(!empty($categories)) $categories = FrontendModel::buildActionURL($categories, 'blog', 'category', 'url', 'url');

		// assign comments
		$this->tpl->assign('widgetBlogCategories', $categories);
	}
}

?>