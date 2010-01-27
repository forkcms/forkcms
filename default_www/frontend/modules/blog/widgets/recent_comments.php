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
	 * Execute the extra
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, will add JS
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
		// @todo make this work in a decent way
		$recentComments = array(
								array('author' => 'Android X10', 'url' => '/nl/blog/detail/webdesign-proces-bij-netlash#comment-1', 'entry_title' => 'Webdesign proces bij Netlash'),
								array('author' => 'wannes', 'url' => '/nl/blog/detail/webdesign-proces-bij-netlash#comment-2', 'entry_title' => 'Webdesign proces bij Netlash'),
								array('author' => 'Jan Ottenbourg', 'url' => '/nl/blog/detail/webdesign-proces-bij-netlash#comment-3', 'entry_title' => 'Webdesign proces bij Netlash'),
								array('author' => 'Jan Seurinck', 'url' => '/nl/blog/detail/webdesign-proces-bij-netlash#comment-4', 'entry_title' => 'Webdesign proces bij Netlash'),
								array('author' => 'Thomas', 'url' => '/nl/blog/detail/webdesign-proces-bij-netlash#comment-5', 'entry_title' => 'Webdesign proces bij Netlash')
							);

		// assign
		$this->tpl->assign('recentComments', $recentComments);
	}

}
?>