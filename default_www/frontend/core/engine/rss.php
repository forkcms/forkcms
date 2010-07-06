<?php

/**
 * FrontendRSS, this is our extended version of SpoonRSS
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendRSS extends SpoonFeedRSS
{
	/**
	 * The default constructor
	 *
	 * @return	void
	 * @param	string $title			The title off the feed.
	 * @param	string $link			The link of the feed.
	 * @param	string $description		The description of the feed.
	 * @param	array[optional] $items	An array with SpoonRSSItems.
	 */
	public function __construct($title, $link, $description, array $items = array())
	{
		// call the parent
		parent::__construct($title, $link, $description, $items);

		// set feed properties
		$this->setLanguage(FRONTEND_LANGUAGE);
		$this->setCopyright(SpoonDate::getDate('Y') .' '. FrontendModel::getModuleSetting('core', 'site_title_'. FRONTEND_LANGUAGE));
		$this->setGenerator(SITE_RSS_GENERATOR);
		$this->setImage(SITE_URL . FRONTEND_CORE_URL .'/layout/images/rss_image.png', $title, $link);
	}
}


/**
 * FrontendRSSItem, this is our extended version of SpoonRSSItem
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendRSSItem extends SpoonFeedRSSItem
{
	/**
	 * Default constructor.
	 *
	 * @return	void
	 * @param	string $title			The title for the item.
	 * @param	string $link			The link for the item.
	 * @param	string $description		The content for the item.
	 */
	public function __construct($title, $link, $description)
	{
		// call parent
		parent::__construct($title, $link, $description);

		// set some properties
		$this->setGuid($link, true);
	}


	/**
	 * Set the author.
	 *
	 * @return	void
	 * @param	string $author
	 */
	public function setAuthor($author)
	{
		// redefine
		$author = (string) $author;

		// add fake-emailaddress
		if(!SpoonFilter::isEmail($author)) $author = SpoonFilter::urlise($author) .'@example.com ('. $author .')';

		// set author
		parent::setAuthor($author);
	}


	/**
	 * Set the description.
	 * All links and images that link to internal files will be prepended with the sites URL
	 *
	 * @return	void
	 * @param	string $description		The content of the item.
	 */
	public function setDescription($description)
	{
		// replace URLs and images
		$search = array('href="/', 'src="/');
		$replace = array('href="'. SITE_URL .'/', 'src="'. SITE_URL .'/');

		// replace links to files
		$description = str_replace($search, $replace, $description);

		// call parent
		parent::setDescription($description);
	}


	/**
	 * Set the guid.
	 * If the link is an internal link the sites URL will be prepended.
	 *
	 * @return	void
	 * @param	string $link					The guid for an item
	 * @param	bool[optional] $isPermaLink		Is this link permanent?
	 */
	public function setGuid($link, $isPermaLink = true)
	{
		// redefine var
		$link = (string) $link;

		// if link doesn't start with http, we prepend the URL of the site
		if(substr($link, 0, 7) != 'http://') $link = SITE_URL . $link;

		// call parent
		parent::setGuid($link, $isPermaLink);
	}


	/**
	 * Set the link.
	 * If the link is an internal link the sites URL will be prepended.
	 *
	 * @return	void
	 * @param	string $link	The link for the item.
	 */
	public function setLink($link)
	{
		// redefine var
		$link = (string) $link;

		// if link doesn't start with http, we prepend the URL of the site
		if(substr($link, 0, 7) != 'http://') $link = SITE_URL . $link;

		// call parent
		parent::setLink($link);
	}
}

?>