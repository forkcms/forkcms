<?php

/**
 * Displays a list of featured blog posts.
 *
 * @author Jeroen Van den Bossche <jeroen.vandenbossche@wijs.be>
 */
class FrontendBlogWidgetFeatured extends FrontendBaseWidget
{
	/**
	 * Constanst to determine how many articles should be collected.
	 *
	 * @var int
	 */
	const NUM_FEATURED_BLOG_POSTS = 5;

	/**
	 * Execute the widget.
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->parse();
	}

	/**
	 * Parse the widget.
	 */
	protected function parse()
	{
		$this->tpl->assign('blogWidgetFeatured', FrontendBlogModel::getFeatured(self::NUM_FEATURED_BLOG_POSTS));
	}
}
