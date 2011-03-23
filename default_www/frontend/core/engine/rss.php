<?php

/**
 * Frontend RSS class.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
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
		// decode
		$title = SpoonFilter::htmlspecialcharsDecode($title);
		$description = SpoonFilter::htmlspecialcharsDecode($description);

		// add UTM-parameters
		$link = FrontendModel::addURLParameters($link, array('utm_source' => 'feed', 'utm_medium' => 'rss', 'utm_campaign' => SpoonFilter::urlise($title)));

		// call the parent
		parent::__construct($title, $link, $description, $items);

		// set feed properties
		$this->setLanguage(FRONTEND_LANGUAGE);
		$this->setCopyright(SpoonDate::getDate('Y') . ' ' . SpoonFilter::htmlspecialcharsDecode(FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE)));
		$this->setGenerator(SITE_RSS_GENERATOR);
		$this->setImage(SITE_URL . FRONTEND_CORE_URL . '/layout/images/rss_image.png', $title, $link);

		// theme was set
		if(FrontendModel::getModuleSetting('core', 'theme', null) != null)
		{
			// theme name
			$theme = FrontendModel::getModuleSetting('core', 'theme', null);

			// theme rss image exists
			if(SpoonFile::exists(PATH_WWW . '/frontend/themes/' . $theme . '/core/images/rss_image.png'))
			{
				// set rss image
				$this->setImage(SITE_URL . '/frontend/themes/' . $theme . '/core/images/rss_image.png', $title, $link);
			}
		}
	}
}


/**
 * FrontendRSSItem, this is our extended version of SpoonRSSItem
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @since		2.0
 */
class FrontendRSSItem extends SpoonFeedRSSItem
{
	/**
	 * Initial values for UTM-parameters
	 *
	 * @var	array
	 */
	private $utm = array('utm_source' => 'feed', 'utm_medium' => 'rss');


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
		// decode
		$title = SpoonFilter::htmlspecialcharsDecode($title);
		$description = SpoonFilter::htmlspecialcharsDecode($description);

		// set UTM-campaign
		$this->utm['utm_campaign'] = SpoonFilter::urlise($title);

		// call parent
		parent::__construct($title, FrontendModel::addURLParameters($link, $this->utm), $description);

		// set some properties
		$this->setGuid($link, true);
	}


	/**
	 * Process links, will prepend SITE_URL if needed and append UTM-parameters
	 *
	 * @return	string
	 * @param	string $content		The content to process.
	 */
	public function processLinks($content)
	{
		// redefine
		$content = (string) $content;

		// replace URLs and images
		$search = array('href="/', 'src="/');
		$replace = array('href="' . SITE_URL . '/', 'src="' . SITE_URL . '/');

		// replace links to files
		$content = str_replace($search, $replace, $content);

		// init var
		$matches = array();

		// match links
		preg_match_all('/href="(http:\/\/(.*))"/iU', $content, $matches);

		// any links?
		if(isset($matches[1]) && !empty($matches[1]))
		{
			// init vars
			$searchLinks = array();
			$replaceLinks = array();

			// loop old links
			foreach($matches[1] as $i => $link)
			{
				$searchLinks[] = $matches[0][$i];
				$replaceLinks[] = 'href="' . FrontendModel::addURLParameters($link, $this->utm) . '"';
			}

			// replace
			$content = str_replace($searchLinks, $replaceLinks, $content);
		}

		// return content
		return $content;
	}


	/**
	 * Set the author.
	 *
	 * @return	void
	 * @param	string $author		The author to use.
	 */
	public function setAuthor($author)
	{
		// remove special chars
		$author = (string) SpoonFilter::htmlspecialcharsDecode($author);

		// add fake-emailaddress
		if(!SpoonFilter::isEmail($author)) $author = SpoonFilter::urlise($author) . '@example.com (' . $author . ')';

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
		// remove special chars
		$description = (string) SpoonFilter::htmlspecialcharsDecode($description);

		// process links
		$description = $this->processLinks($description);

		// call parent
		parent::setDescription($description);
	}


	/**
	 * Set the guid.
	 * If the link is an internal link the sites URL will be prepended.
	 *
	 * @return	void
	 * @param	string $link					The guid for an item.
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