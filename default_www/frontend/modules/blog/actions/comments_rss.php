<?php

/**
 * This is the RSS-feed with all the comments
 *
 * @package		frontend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendBlogCommentsRSS extends FrontendBaseBlock
{
	/**
	 * The comments
	 *
	 * @var	array
	 */
	private $items;


	/**
	 * Execute the extra
	 *
	 * @return	void
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
	 *
	 * @return	void
	 */
	private function getData()
	{
		// get articles
		$this->items = FrontendBlogModel::getAllComments();
	}


	/**
	 * Parse the data into the template
	 *
	 * @return	void
	 */
	private function parse()
	{
		// get vars
		$title = ucfirst(FL::msg('BlogAllComments'));
		$link = SITE_URL . FrontendNavigation::getURLForBlock('blog');
		$detailLink = SITE_URL . FrontendNavigation::getURLForBlock('blog', 'detail');
		$description = null;

		// create new rss instance
		$rss = new FrontendRSS($title, $link, $description);

		// loop articles
		foreach($this->items as $item)
		{
			// init vars
			$title = $item['author'] . ' ' . FL::lbl('On') . ' ' . $item['post_title'];
			$link = $detailLink . '/' . $item['post_url'] . '/#comment-' . $item['id'];
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

?>