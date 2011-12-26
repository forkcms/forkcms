<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget with recent events-articles
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendEventsWidgetRecentArticlesFull extends FrontendBaseWidget
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
		$rssLink = FrontendModel::getModuleSetting('events', 'feedburner_url_' . FRONTEND_LANGUAGE);
		if($rssLink == '') $rssLink = FrontendNavigation::getURLForBlock('events', 'rss');

		// add RSS-feed
		$this->header->addLink(array('rel' => 'alternate', 'type' => 'application/rss+xml', 'title' => FrontendModel::getModuleSetting('events', 'rss_title_' . FRONTEND_LANGUAGE), 'href' => $rssLink), true);

		// assign comments
		$this->tpl->assign('widgetEventsRecentArticlesFull', FrontendEventsModel::getAll(FrontendModel::getModuleSetting('events', 'recent_articles_full_num_items', 5)));
	}
}
