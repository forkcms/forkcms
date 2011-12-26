<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the RSS-feed with all the comments
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendEventsCommentsRss extends FrontendBaseBlock
{
	/**
	 * The comments
	 *
	 * @var	array
	 */
	private $items;

	/**
	 * Execute the extra
	 */
	public function execute()
	{
		// call the parent
		parent::execute();

		// load the data
		$this->getData();

		// parse
		$this->parse();
	}

	/**
	 * Load the data, don't forget to validate the incoming data
	 */
	private function getData()
	{
		// get articles
		$this->items = FrontendEventsModel::getAllComments();
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// get vars
		$title = SpoonFilter::ucfirst(FL::msg('EventsAllComments'));
		$link = SITE_URL . FrontendNavigation::getURLForBlock('events');
		$detailLink = SITE_URL . FrontendNavigation::getURLForBlock('events', 'detail');
		$description = null;

		// create new rss instance
		$rss = new FrontendRSS($title, $link, $description);

		// loop articles
		foreach($this->items as $item)
		{
			// init vars
			$title = $item['author'] . ' ' . FL::lbl('On') . ' ' . $item['event_title'];
			$link = $detailLink . '/' . $item['event_url'] . '/#comment-' . $item['id'];
			$description = $item['text'];

			// create new instance
			$rssItem = new FrontendRSSItem($title, $link, $description);

			// set item properties
			$rssItem->setPublicationDate($item['created_on']);
			$rssItem->setAuthor($item['author']);

			// add item
			$rss->addItem($rssItem);
		}

		// output
		$rss->parse();
	}
}
