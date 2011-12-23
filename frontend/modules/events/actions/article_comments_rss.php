<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the RSS-feed for comments on a certain item.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendEventsArticleCommentsRSS extends FrontendBaseBlock
{
	/**
	 * The record
	 *
	 * @var array
	 */
	private $record;

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
		// validate incoming parameters
		if($this->URL->getParameter(1) === null) $this->redirect(FrontendNavigation::getURL(404));

		// get record
		$this->record = FrontendEventsModel::get($this->URL->getParameter(1));

		// anything found?
		if(empty($this->record)) $this->redirect(FrontendNavigation::getURL(404));

		// get articles
		$this->items = FrontendEventsModel::getComments($this->record['id']);
	}

	/**
	 * Parse the data into the template
	 */
	private function parse()
	{
		// get vars
		$title = vsprintf(FL::msg('CommentsOn'), array($this->record['title']));
		$link = SITE_URL . FrontendNavigation::getURLForBlock('events', 'article_comments_rss') . '/' . $this->record['url'];
		$detailLink = SITE_URL . FrontendNavigation::getURLForBlock('events', 'detail');
		$description = null;

		// create new rss instance
		$rss = new FrontendRSS($title, $link, $description);

		// loop articles
		foreach($this->items as $item)
		{
			// init vars
			$title = $item['author'] . ' ' . FL::lbl('On') . ' ' . $this->record['title'];
			$link = $detailLink . '/' . $this->record['url'] . '/#comment-' . $item['id'];
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
