<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Frontend Ical class.
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
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
	 * @param string $title The title for the calendar.
	 * @param string $description A description for the calendar.
	 */
	public function __construct($title, $description)
	{
		// redefine
		$title = (string) $title;
		$description = (string) $description;

		// convert to plain text
		$description = FrontendModel::convertToPlainText($description);

		// set some basic stuf
		$this->setProductIdentifier('Fork v' . FORK_VERSION);

		// build properties
		$properties['X-WR-CALNAME;VALUE=TEXT'] = $title;
		$properties['X-WR-CALDESC'] = $description;
		$properties['X-WR-TIMEZONE'] = date_default_timezone_get();

		// set the title
		$this->setTitle($title);

		// set properties
		$this->setXProperties($properties);

		// set the filename
		$this->setFilename(str_replace('-', '_', SpoonFilter::urlise($title)) . '.ics');
	}

	/**
	 * Get the title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Parse the ical and output into the browser.
	 *
	 * @param bool[optional] $headers Should the headers be set? (Use false if you're debugging).
	 */
	public function parse($headers = true)
	{
		// set headers
		if((bool) $headers) SpoonHTTP::setHeaders('Content-Disposition: inline; filename=' . SpoonFilter::urlise($this->getTitle()) . '.ics');

		// call the parent
		parent::parse($headers);
	}

	/**
	 * Set the title
	 *
	 * @param string $title The title for the calendar.
	 */
	public function setTitle($title)
	{
		$this->title = (string) $title;
	}
}

/**
 * FrontendIcalItem, this is our extended version of SpoonIcalItemEvent
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendIcalEvent extends SpoonIcalEvent
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
	 * @param string $title The title for the item.
	 * @param string $link The link for the item.
	 * @param string $description The content for the item.
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

		// set identifier
		$this->setUniqueIdentifier(md5($link));

		// build properties
		$properties['X-GOOGLE-CALENDAR-CONTENT-TITLE'] = $title;
		$properties['X-GOOGLE-CALENDAR-CONTENT-ICON'] = SITE_URL . '/favicon.ico';
		$properties['X-GOOGLE-CALENDAR-CONTENT-URL'] = $this->getUrl();

		// set properties
		$this->setXProperties($properties);
	}

	/**
	 * Process links, will prepend SITE_URL if needed and append UTM-parameters
	 *
	 * @param string $content The content to process.
	 * @return string
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
	 * Set the url
	 *
	 * @param string $url The url to assiociate the item with.
	 */
	public function setUrl($url)
	{
		// redefine var
		$url = (string) $url;

		// if link doesn't start with http, we prepend the URL of the site
		if(substr($url, 0, 7) != 'http://') $url = SITE_URL . $url;

		$url = FrontendModel::addURLParameters($url, $this->utm);
		$url = htmlspecialchars_decode($url);

		// call parent
		parent::setUrl($url);
	}
}
