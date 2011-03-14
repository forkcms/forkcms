<?php

/**
 * This is a widget with recent blog-articles
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBlogWidgetRecentArticlesList extends FrontendBaseWidget
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
		// get RSS-link
		$rssLink = FrontendModel::getModuleSetting('blog', 'feedburner_url_' . FRONTEND_LANGUAGE);
		if($rssLink == '') $rssLink = FrontendNavigation::getURLForBlock('blog', 'rss');

		// add RSS-feed into the metaCustom
		$this->header->addMetaCustom('<link rel="alternate" type="application/rss+xml" title="' . FrontendModel::getModuleSetting('blog', 'rss_title_' . FRONTEND_LANGUAGE) . '" href="' . $rssLink . '" />');

		// assign comments
		$this->tpl->assign('widgetBlogRecentArticlesList', FrontendBlogModel::getAll(FrontendModel::getModuleSetting('blog', 'recent_articles_list_num_items', 5)));
	}
}

?>