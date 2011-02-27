<?php

/**
 * Frontend Ical class.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class FrontendIcal extends SpoonIcal
{
	/**
	 * The title
	 *
	 * @var	string
	 */
	private $title;


	/**
	 * Default constructor
	 *
	 * @param	string $title			The title for the calendar.
	 * @param	string $description		A description for the calendar.
	 */
	public function __construct($title, $description)
	{
		// redefine
		$title = (string) $title;
		$description = (string) $description;

		// convert to plain text
		$description = FrontendModel::convertToPlainText($description);

		// set some basic stuf
		$this->setProductIdentifier('Fork v'. FORK_VERSION);

		// build properties
		$properties['X-WR-CALNAME;VALUE=TEXT'] = $title;
		$properties['X-WR-CALDESC'] = $description;
		$properties['X-WR-TIMEZONE'] = date_default_timezone_get();

		// set the title
		$this->setTitle($title);

		// set properties
		$this->setXProperties($properties);
	}


	/**
	 * Get the title
	 *
	 * @return	string
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * Parse the ical and output into the browser.
	 *
	 * @return	void
	 * @param	bool[optional] $headers		Should the headers be set? (Use false if you're debugging).
	 */
	public function parse($headers = true)
	{
		// set headers
		if((bool) $headers) SpoonHTTP::setHeaders('Content-Disposition: inline; filename='. SpoonFilter::urlise($this->getTitle()) . '.ics');

		// call the parent
		parent::parse($headers);
	}


	/**
	 * Set the title
	 *
	 * @param	string $title	The title for the calendar.
	 */
	public function setTitle($title)
	{
		$this->title = (string) $title;
	}
}


/**
 * FrontendIcalItem, this is our extended version of SpoonIcalItem
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class FrontendIcalItemEvent extends SpoonIcalItemEvent
{
	/**
	 * Initial values for UTM-parameters
	 *
	 * @var	array
	 */
	private $utm = array('utm_source' => 'feed', 'utm_medium' => 'ical');


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
		// set UTM-campaign
		$this->utm['utm_campaign'] = SpoonFilter::urlise($title);

		// convert to plain text
		$description = FrontendModel::convertToPlainText($description);

		// set title
		$this->setSummary($title);

		// set url
		$this->setUrl(FrontendModel::addURLParameters($link, $this->utm));

		// set description
		$this->setDescription($this->processLinks($description));

		// set organiser
		$siteTitle = FrontendModel::getModuleSetting('core', 'site_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE);
		$from = FrontendModel::getModuleSetting('core', 'mailer_reply_to');
		$sentBy = FrontendModel::getModuleSetting('core', 'mailer_from');
		$this->setOrganizer($from['email'], $siteTitle, null, $sentBy['email'], FRONTEND_LANGUAGE);

		// set identifier
		$this->setUniqueIdentifier(md5($link));

		// build properties
		$properties['X-GOOGLE-CALENDAR-CONTENT-TITLE'] = SpoonIcal::formatAsString($title);
		$properties['X-GOOGLE-CALENDAR-CONTENT-ICON'] = SpoonIcal::formatAsString(SITE_URL .'/favicon.ico');
		$properties['X-GOOGLE-CALENDAR-CONTENT-URL'] = SpoonIcal::formatAsString($this->getUrl());
		$properties['X-GOOGLE-CALENDAR-CONTENT-TYPE'] = 'text/html';

		// set properties
		$this->setXProperties($properties);
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
		$replace = array('href="'. SITE_URL .'/', 'src="'. SITE_URL .'/');

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
				$replaceLinks[] = 'href="'. FrontendModel::addURLParameters($link, $this->utm) .'"';
			}

			// replace
			$content = str_replace($searchLinks, $replaceLinks, $content);
		}

		// return content
		return $content;
	}


	/**
	 * Set the url
	 *
	 * @return	void
	 * @param	string $url		The url to assiociate the item with.
	 */
	public function setUrl($url)
	{
		// redefine var
		$url = (string) $url;

		// if link doesn't start with http, we prepend the URL of the site
		if(substr($url, 0, 7) != 'http://') $url = SITE_URL . $url;

		// call parent
		parent::setUrl(FrontendModel::addURLParameters($url, $this->utm));
	}
}

?>