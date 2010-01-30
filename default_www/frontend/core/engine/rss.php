<?php

// require SpoonRSS
require_once 'spoon/feed/rss.php';

/**
 * FrontendRSS, this is our extended version of SpoonRSS
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendRSS extends SpoonRSS
{
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
class FrontendRSSItem extends SpoonRSSItem
{
	/**
	 * Default constructor.
	 *
	 * @return	void
	 * @param	string $title
	 * @param	string $link
	 * @param	string $description
	 */
	public function __construct($title, $link, $description)
	{
		// call parent
		parent::__construct($title, $link, $description);

		// set some properties
		$this->setGuid($link, true);
	}

	/**
	 * Set the description.
	 *
	 * @return	void
	 * @param	string $description
	 */
	public function setDescription($description)
	{
		// replace urls and images
		$search = array('href="/', 'src="/');
		$replace = array('href="'. SITE_URL .'/', 'src="'. SITE_URL .'/');

		// replace links to files
		$description = str_replace($search, $replace, $description);

		// call parent
		parent::setDescription($description);
	}


	/**
	 * Set the guid.
	 *
	 * @return	void
	 * @param	string $link
	 * @param	bool[optional] $isPermaLink
	 */
	public function setGuid($link, $isPermaLink = true)
	{
		// redefine var
		$link = (string) $link;

		// if link doesn't start with http, we prepend the url of the site
		if(substr($link, 0, 7) != 'http://') $link = SITE_URL . $link;

		// call parent
		parent::setGuid($link, $isPermaLink);
	}


	/**
	 * Set the link.
	 *
	 * @return	void
	 * @param	string $link
	 */
	public function setLink($link)
	{
		// redefine var
		$link = (string) $link;

		// if link doesn't start with http, we prepend the url of the site
		if(substr($link, 0, 7) != 'http://') $link = SITE_URL . $link;

		// call parent
		parent::setLink($link);
	}
}

?>