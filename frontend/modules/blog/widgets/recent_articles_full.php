<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with recent blog-articles
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendBlogWidgetRecentArticlesFull extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->parse();
	}

	/**
	 * Parse
	 */
	private function parse()
	{
		// get RSS-link
		$rssLink = FrontendModel::getModuleSetting('blog', 'feedburner_url_' . FRONTEND_LANGUAGE);
		if($rssLink == '') $rssLink = FrontendNavigation::getURLForBlock('blog', 'rss');

		// add RSS-feed
		$this->header->addLink(array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => FrontendModel::getModuleSetting('blog', 'rss_title_' . FRONTEND_LANGUAGE), 'href' => $rssLink), true);

		// assign comments
		$this->tpl->assign('widgetBlogRecentArticlesFull', FrontendBlogModel::getAll(FrontendModel::getModuleSetting('blog', 'recent_articles_full_num_items', 5)));
		$this->tpl->assign('widgetBlogRecentArticlesFullRssLink', $rssLink);
	}
}
