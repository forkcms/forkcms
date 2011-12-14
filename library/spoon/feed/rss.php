<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	feed
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.1.0
 */


/**
 * This base class provides all the methods used by RSS-files
 *
 * @package		spoon
 * @subpackage	feed
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.1.0
 */
class SpoonFeedRSS
{
	/**
	 * XML header
	 *
	 * @var	string
	 */
	const HEADER = "Content-Type: application/xml; charset=";


	/**
	 * Categories
	 *
	 * @var	array
	 */
	private $categories = array();


	/**
	 * The charset
	 *
	 * @var	string
	 */
	private $charset = 'utf-8';


	/**
	 * Cloud properties
	 *
	 * @var	array
	 */
	private $cloud;


	/**
	 * Copyright
	 *
	 * @var	string
	 */
	private $copyright;


	/**
	 * Description
	 *
	 * @var	string
	 */
	private $description;


	/**
	 * Docs
	 *
	 * @var	string
	 */
	private $docs;


	/**
	 * Generator
	 *
	 * @var	string
	 */
	private $generator;


	/**
	 * Image properties
	 *
	 * @var	array
	 */
	private $image = array();


	/**
	 * Items
	 *
	 * @var	array
	 */
	private $items = array();


	/**
	 * Language
	 *
	 * @var	string
	 */
	private $language;


	/**
	 * Last build date
	 *
	 * @var	string
	 */
	private $lastBuildDate;


	/**
	 * Link
	 *
	 * @var	string
	 */
	private $link;


	/**
	 * Managing editor
	 *
	 * @var	string
	 */
	private $managingEditor;


	/**
	 * Publication date
	 *
	 * @var	int
	 */
	private $publicationDate;


	/**
	 * Rating
	 *
	 * @var	string
	 */
	private $rating;


	/**
	 * Days that will be skipped
	 *
	 * @var	array
	 */
	private $skipDays = array();


	/**
	 * Hours that will be skipped
	 *
	 * @var	array
	 */
	private $skipHours = array();


	/**
	 * Should the items be sorted on the publication date?
	 *
	 * @var	bool
	 */
	private $sort = true;


	/**
	 * The sortingmethod (used in compare-method)
	 *
	 * @var	string
	 */
	private static $sortingMethod = 'desc';


	/**
	 * Title
	 *
	 * @var	string
	 */
	private $title;


	/**
	 * Time to life
	 *
	 * @var	int
	 */
	private $ttl;


	/**
	 * Webmaster
	 *
	 * @var	string
	 */
	private $webmaster;


	/**
	 * The default constructor
	 *
	 * @param	string $title			The title off the feed.
	 * @param	string $link			The link of the feed.
	 * @param	string $description		The description of the feed.
	 * @param	array[optional] $items	An array with SpoonFeedRSSItems.
	 */
	public function __construct($title, $link, $description, array $items = array())
	{
		// set properties
		$this->setTitle($title);
		$this->setLink($link);
		$this->setDescription($description);

		// loop items and add them
		foreach($items as $item) $this->addItem($item);
	}


	/**
	 * Adds a category for the feed.
	 *
	 * @param	string $category			The name of the category.
	 * @param	string[optional] $domain	A domain that idenitifies a categorization taxonomy.
	 */
	public function addCategory($category, $domain = null)
	{
		// init var
		$categoryDetails = array();

		// add category
		$categoryDetails['category'] = (string) $category;

		// add domain (optional)
		if($domain !== null) $categoryDetails['domain'] = (string) $domain;

		// set property
		$this->categories[] = $categoryDetails;
	}


	/**
	 * Add an item to the feed.
	 *
	 * @param	SpoonFeedRSSItem $item		A SpoonFeedRSSItem that represents a single article in the feed.
	 */
	public function addItem(SpoonFeedRSSItem $item)
	{
		$this->items[] = $item;
	}


	/**
	 * Add a day to skip. The default value is sunday.
	 *
	 * @param	string $day		Add a day where aggregators should skip updating the feed.
	 */
	public function addSkipDay($day)
	{
		// allowed days
		$allowedDays = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saterday');

		// redefine var
		$day = (string) SpoonFilter::getValue(strtolower($day), $allowedDays, 'sunday');

		// validate
		if(in_array($day, $this->skipDays)) throw new SpoonFeedException('This (' . $day . ') day was already added.');

		// set property
		$this->skipDays[] = SpoonFilter::ucfirst($day);
	}


	/**
	 * Add a hour to skip, default is 0.
	 *
	 * @param 	int $hour	Add an hour where aggregators should skip updating the feed.
	 */
	public function addSkipHour($hour)
	{
		// allowed hours
		$allowedHours = range(0, 23);

		// redefine var
		$hour = (int) SpoonFilter::getValue($hour, $allowedHours, 0);

		// validate
		if(!in_array($hour, $allowedHours)) throw new SpoonFeedException('This (' . $hour . ') isn\'t a valid hour. Only ' . join(', ', $allowedHours) . ' are allowed.)');
		if(in_array($hour, $this->skipHours)) throw new SpoonFeedException('This (' . $hour . ') hour is already added.');

		// set property
		$this->skipHours[] = (int) $hour;
	}


	/**
	 * Build the xmlfile
	 *
	 * @return	string	A string that represents the fully build XML.
	 */
	protected function buildXML()
	{
		// sort if needed
		if($this->getSorting()) $this->sort();

		// init xml
		$XML = '<?xml version="1.0" encoding="' . strtolower($this->getCharset()) . '" ?>' . "\n";
		$XML .= '<rss version="2.0">' . "\n";
		$XML .= '<channel>' . "\n";

		// insert title
		$XML .= '	<title><![CDATA[' . $this->getTitle() . ']]></title>' . "\n";

		// insert link
		$XML .= '	<link>' . $this->getLink() . '</link>' . "\n";

		// insert description
		$XML .= '	<description>' . "\n";
		$XML .= '		<![CDATA[' . "\n";
		$XML .= '		' . $this->getDescription() . "\n";
		$XML .= '		]]>' . "\n";
		$XML .= '</description>' . "\n";

		// fetch image properties
		$imageProperties = $this->getImage();

		// insert image if needed
		if(!empty($imageProperties))
		{
			$image = $this->getImage();

			$XML .= '	<image>' . "\n";
			$XML .= '		<title><![CDATA[' . $image['title'] . ']]></title>' . "\n";
			$XML .= '		<url>' . $image['url'] . '</url>' . "\n";
			$XML .= '		<link>' . $image['link'] . '</link>' . "\n";
			if(isset($image['width']) && $image['width'] != '') $XML .= '		<width>' . $image['title'] . '</width>' . "\n";
			if(isset($image['height']) && $image['height'] != '') $XML .= '		<height>' . $image['title'] . '</height>' . "\n";
			if(isset($image['description']) && $image['description'] != '') $XML .= '		<description><![CDATA[' . $image['title'] . ']]></description>' . "\n";
			$XML .= '	</image>' . "\n";
		}

		// insert last build date
		if($this->getLastBuildDate() != '') $XML .= '	<lastBuildDate>' . $this->getLastBuildDate('r') . '</lastBuildDate>' . "\n";

		// insert publication date
		if($this->getPublicationDate() != '') $XML .= '	<pubDate>' . $this->getPublicationDate('r') . '</pubDate>' . "\n";

		// insert time to live
		if($this->getTTL() != '') $XML .= '	<ttl>' . $this->getTTL() . '</ttl>' . "\n";

		// insert managing editor
		if($this->getManagingEditor() != '') $XML .= '	<managingEditor>' . $this->getManagingEditor() . '</managingEditor>' . "\n";

		// insert webmaster
		if($this->getWebmaster() != '') $XML .= '	<webmaster>' . $this->getWebmaster() . '></webmaster>' . "\n";

		// insert copyright
		if($this->getCopyright() != '') $XML .= '	<copyright>' . $this->getCopyright() . '</copyright>' . "\n";

		// fetch categories
		$categories = $this->getCategories();

		// insert categories
		if(!empty($categories))
		{
			foreach($this->getCategories() as $category)
			{
				if(isset($category['domain']) && $category['domain'] != '') $XML .= '	<category domain="' . $category['domain'] . '"><![CDATA[' . $category['category'] . ']]></category>' . "\n";
				else $XML .= '	<category><![CDATA[' . $category['category'] . ']]</category>' . "\n";
			}
		}

		// insert rating
		if($this->getRating() != '') $XML .= '	<rating>' . $this->getRating() . '</rating>' . "\n";

		// insert generator
		if($this->getGenerator() != '') $XML .= '	<generator><![CDATA[' . $this->getGenerator() . ']]></generator>' . "\n";

		// insert language
		if($this->getLanguage() != '') $XML .= '	<language>' . $this->getLanguage() . '</language>' . "\n";

		// insert docs
		if($this->getDocs() != '') $XML .= '	<docs>' . $this->getDocs() . '</docs>' . "\n";

		// insert skipdays
		$skipDays = $this->getSkipDays();
		if(!empty($skipDays))
		{
			$XML .= '	<skipDays>' . "\n";
			foreach($skipDays as $day) $XML .= '	<day>' . $day . '</day>' . "\n";
			$XML .= '	</skipDays>' . "\n";
		}

		// insert skiphours
		$skipHours = $this->getSkipHours();
		if(!empty($skipHours))
		{
			$XML .= '	<skipHours>' . "\n";
			foreach($skipHours as $hour) $XML .= '	<hour>' . $hour . '</hour>' . "\n";
			$XML .= '	</skipHours>' . "\n";
		}

		// insert cloud
		$cloudProperties = $this->getCloud();
		if(!empty($cloudProperties))
		{
			$cloud = $this->getCloud();
			$XML .= '	<cloud domain="' . $cloud['domain'] . '" port="' . $cloud['port'] . '" path="' . $cloud['path'] . '" registerProce-dure="' . $cloud['register_procedure'] . '" protocol="' . $cloud['protocol'] . '" />' . "\n";
		}

		// insert items
		foreach($this->getItems() as $item)
		{
			$XML .= $item->parse();
		}

		// add endtags
		$XML .= '</channel>' . "\n";
		$XML .= '</rss>' . "\n";

		return $XML;
	}


	/**
	 * Compare objects for sorting on publication date.
	 *
	 * @return	int								An integer used for sorting.
	 * @param	SpoonFeedRSSItem $object1		The first element.
	 * @param	SpoonFeedRSSItem $object2		The second element.
	 */
	private static function compareObjects(SpoonFeedRSSItem $object1, SpoonFeedRSSItem $object2)
	{
		// if the object have the same publicationdate there are equal
		if($object1->getPublicationDate() == $object2->getPublicationDate()) return 0;

		// sort ascending
		if(self::$sortingMethod == 'asc')
		{
			// if the publication date is greater then the other return 1, so we known howto sort
			if($object1->getPublicationDate() > $object2->getPublicationDate()) return 1;

			// if the publication date is smaller then the other return -1, so we known howto sort
			if($object1->getPublicationDate() < $object2->getPublicationDate()) return -1;
		}

		// sort descending
		else
		{
			// if the publication date is greater then the other return -1, so we known howto sort
			if($object1->getPublicationDate() > $object2->getPublicationDate()) return -1;

			// if the publication date is smaller then the other return 1, so we known howto sort
			if($object1->getPublicationDate() < $object2->getPublicationDate()) return 1;
		}
	}


	/**
	 * Retrieves the categories for a feed.
	 *
	 * @return	array
	 */
	public function getCategories()
	{
		return $this->categories;
	}


	/**
	 * Get the charset.
	 *
	 * @return	string
	 */
	public function getCharset()
	{
		return $this->charset;
	}


	/**
	 * Get the cloud.
	 *
	 * @return	array
	 */
	public function getCloud()
	{
		return $this->cloud;
	}


	/**
	 * Get the copyright.
	 *
	 * @return	string
	 */
	public function getCopyright()
	{
		return $this->copyright;
	}


	/**
	 * Get the description.
	 *
	 * @return	string
	 */
	public function getDescription()
	{
		return $this->description;
	}


	/**
	 * Get the docs.
	 *
	 * @return	string
	 */
	public function getDocs()
	{
		return $this->docs;
	}


	/**
	 * Get the generator.
	 *
	 * @return	string
	 */
	public function getGenerator()
	{
		return $this->generator;
	}


	/**
	 * Retrieves the image properties.
	 *
	 * @return	array
	 */
	public function getImage()
	{
		return $this->image;
	}


	/**
	 * Retrieves the items.
	 *
	 * @return	array
	 */
	public function getItems()
	{
		return $this->items;
	}


	/**
	 * Get the language.
	 *
	 * @return	string
	 */
	public function getLanguage()
	{
		return $this->language;
	}


	/**
	 * Get the last build date.
	 *
	 * @return	mixed						If no format is specified the date will be returned as a UNIX timestamp.
	 * @param	string[optional] $format	The format for the date.
	 */
	public function getLastBuildDate($format = null)
	{
		// set time if needed
		if($this->lastBuildDate == null) $this->lastBuildDate = time();

		// format if needed
		if($format) $date = date((string) $format, $this->lastBuildDate);
		else $date = $this->lastBuildDate;

		// return
		return $date;
	}


	/**
	 * Get the link.
	 *
	 * @return	string
	 */
	public function getLink()
	{
		return $this->link;
	}


	/**
	 * Get the managing editor.
	 *
	 * @return	string
	 */
	public function getManagingEditor()
	{
		return $this->managingEditor;
	}


	/**
	 * Get the publication date.
	 *
	 * @return	mixed						If no format is specified the date will be returned as a UNIX timestamp.
	 * @param	string[optional] $format	The format for the date.
	 */
	public function getPublicationDate($format = null)
	{
		// set time if needed
		if($this->publicationDate == null) $this->publicationDate = time();

		// format if needed
		if($format) $date = date((string) $format, $this->publicationDate);
		else $date = $this->publicationDate;

		// return
		return $date;
	}


	/**
	 * Get the rating
	 *
	 * @return	string
	 */
	public function getRating()
	{
		return $this->rating;
	}


	/**
	 * Get the raw XML.
	 *
	 * @return	string
	 */
	public function getRawXML()
	{
		return $this->buildXML();
	}


	/**
	 * Retrieves the days to skip.
	 *
	 * @return	array	An array with all the days to skip.
	 */
	public function getSkipDays()
	{
		return $this->skipDays;
	}


	/**
	 * Retrieves the hours to skip.
	 *
	 * @return	array	An array with all the hours to skip.
	 */
	public function getSkipHours()
	{
		return $this->skipHours;
	}


	/**
	 * Get sorting status.
	 *
	 * @return	bool
	 */
	public function getSorting()
	{
		return $this->sort;
	}


	/**
	 * Get the sorting method.
	 *
	 * @return	string
	 */
	public function getSortingMethod()
	{
		return self::$sortingMethod;
	}


	/**
	 * Get the title.
	 *
	 * @return	string
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * Get the time to life.
	 *
	 * @return	int		The TTL in seconds.
	 */
	public function getTTL()
	{
		return $this->ttl;
	}


	/**
	 * Get the webmaster.
	 *
	 * @return	string
	 */
	public function getWebmaster()
	{
		return $this->webmaster;
	}


	/**
	 * Checks if the feed is valid.
	 *
	 * @return	bool
	 * @param	string $URL					An URL where the feed is located or the XML of the feed.
	 * @param	string[optional] $type		The type of feed, possible values are: url, string.
	 */
	public static function isValid($URL, $type = 'url')
	{
		// redefine var
		$URL = (string) $URL;
		$type = (string) SpoonFilter::getValue($type, array('url', 'string'), 'url');

		// validate
		if($type == 'url' && !SpoonFilter::isURL($URL)) throw new SpoonFeedException('This (' . $URL . ') isn\'t a valid url.');

		// load xmlstring
		if($type == 'url')
		{
			// check if allow_url_fopen is enabled
			if(ini_get('allow_url_fopen') == 0) throw new SpoonFeedException('allow_url_fopen should be enabled, if you want to get a remote URL.');

			// open the url
			$handle = @fopen($URL, 'r');

			// validate the handle
			if($handle === false) throw new SpoonFeedException('Something went wrong while retrieving the URL.');

			// read the string
			$xmlString = @stream_get_contents($handle);

			// close the hanlde
			@fclose($handle);
		}

		// not that url
		else $xmlString = $URL;

		// convert to simpleXML
		$XML = @simplexml_load_string($xmlString);

		// invalid XML?
		if($XML === false) return false;

		// check if all needed elements are present
		if(!isset($XML->channel) || !isset($XML->channel->title) || !isset($XML->channel->link) || !isset($XML->channel->description) || !isset($XML->channel->item)) return false;

		// loop items
		foreach($XML->channel->item as $item)
		{
			// validate items
			if(!SpoonFeedRSSItem::isValid($item)) return false;
		}

		// fallback
		return true;
	}


	/**
	 * Parse the feed and output the feed into the browser.
	 *
	 * @param	bool[optional] $headers		Should the headers be set? (Use false if you're debugging).
	 */
	public function parse($headers = true)
	{
		// set headers
		if((bool) $headers) SpoonHTTP::setHeaders(self::HEADER . $this->getCharset());

		// output
		echo $this->buildXML();

		// stop here
		exit;
	}


	/**
	 * Write the feed into a file
	 *
	 * @param	string $path	The path (and filename) where the feed should be written.
	 */
	public function parseToFile($path)
	{
		// get xml
		$XML = $this->buildXML();

		// write content
		SpoonFile::setContent((string) $path, $XML, false, true);
	}


	/**
	 * Reads an feed into a SpoonRSS object.
	 *
	 * @return	SpoonRSS					Returns as an instance of SpoonRSS.
	 * @param	string $URL					An URL where the feed is located or the XML of the feed.
	 * @param	string[optional] $type		The type of feed, possible values are: url, string.
	 * @param	bool[optional] $force		Force to read this feed without validation.
	 */
	public static function readFromFeed($URL, $type = 'url', $force = false)
	{
		// redefine var
		$URL = (string) $URL;
		$type = (string) SpoonFilter::getValue($type, array('url', 'string'), 'url');

		// validate
		if($type == 'url' && !SpoonFilter::isURL($URL)) throw new SpoonFeedException('This (' . SpoonFilter::htmlentities($URL) . ') isn\'t a valid URL.');
		if(!$force) if(!self::isValid($URL, $type)) throw new SpoonFeedException('Invalid feed');

		// load xmlstring
		if($type == 'url') $xmlString = SpoonHTTP::getContent($URL);

		// not that url
		else $xmlString = $URL;

		// convert to simpleXML
		$XML = @simplexml_load_string($xmlString);

		// validate the feed
		if($XML === false) throw new SpoonFeedException('Invalid rss-string.');

		// get title, link and description
		$title = (string) $XML->channel->title;
		$link = (string) $XML->channel->link;
		$description = (string) $XML->channel->description;

		// create instance
		$RSS = new SpoonFeedRSS($title, $link, $description);

		// add items
		foreach($XML->channel->item as $item)
		{
			// try to read
			try
			{
				// read xml
				$item = SpoonFeedRSSItem::readFromXML($item);
				$RSS->addItem($item);
			}

			// catch exceptions
			catch(Exception $e)
			{
				// ignore exceptions
			}
		}

		// add category
		if(isset($XML->channel->category))
		{
			foreach($XML->channel->category as $category)
			{
				if(isset($category['domain'])) $RSS->addCategory((string) $category, (string) $category['domain']);
				else $RSS->addCategory((string) $category);
			}
		}

		// add skip day
		if(isset($XML->channel->skipDays))
		{
			// loop ski-days
			foreach($XML->channel->skipDays->day as $day)
			{
				// try to add
				try
				{
					// add skip-day
					$RSS->addSkipDay((string) $day);
				}

				// catch exception
				catch(Exception $e)
				{
					// ignore exceptions
				}
			}
		}

		// add skip hour
		if(isset($XML->channel->skipHours))
		{
			foreach($XML->channel->skipHours->hour as $hour)
			{
				// try to add
				try
				{
					// add skip hour
					$RSS->addSkipHour((int) $hour);
				}

				// catch exception
				catch(Exception $e)
				{
					// ignore exceptions
				}
			}
		}

		// set cloud
		if(isset($XML->channel->cloud['domain']) && isset($XML->channel->cloud['port']) && isset($XML->channel->cloud['path']) && isset($XML->channel->cloud['registerProce-dure']) && isset($XML->channel->cloud['protocol']))
		{
			// read attributes
			$cloudDomain = (string) $XML->channel->cloud['domain'];
			$cloudPort = (int) $XML->channel->cloud['port'];
			$cloudPath = (string) $XML->channel->cloud['path'];
			$cloudRegisterProcedure = (string) $XML->channel->cloud['registerProce-dure'];
			$cloudProtocol = (string) $XML->channel->cloud['protocol'];

			// set property
			$RSS->setCloud($cloudDomain, $cloudPort, $cloudPath, $cloudRegisterProcedure, $cloudProtocol);
		}

		// set copyright
		if(isset($XML->channel->copyright))
		{
			$copyright = (string) $XML->channel->copyright;
			$RSS->setCopyright($copyright);
		}

		// set docs
		if(isset($XML->channel->docs))
		{
			$docs = (string) $XML->channel->docs;
			$RSS->setDocs($docs);
		}

		// set generator if it is present
		if(isset($XML->channel->generator))
		{
			$generator = (string) $XML->channel->generator;
			$RSS->setGenerator($generator);
		}

		// set image if it is present
		if(isset($XML->channel->image->title) && isset($XML->channel->image->url) && isset($XML->channel->image->link))
		{
			// read properties
			$imageTitle = (string) $XML->channel->image->title;
			$imageURL = (string) $XML->channel->image->url;
			$imageLink = (string) $XML->channel->image->link;

			// read optional properties
			if(isset($XML->channel->image->width)) $imageWidth = (int) $XML->channel->image->width;
			else $imageWidth = null;
			if(isset($XML->channel->image->height)) $imageHeight = (int) $XML->channel->image->height;
			else $imageHeight = null;
			if(isset($XML->channel->image->description)) $imageDescription = (string) $XML->channel->image->description;
			else $imageDescription = null;

			// try to set image
			try
			{
				// set image
				$RSS->setImage($imageURL, $imageTitle, $imageLink, $imageWidth, $imageHeight, $imageDescription);
			}

			// catch exception
			catch(Exception $e)
			{
				// ignore exceptions
			}
		}

		// set language if its is present
		if(isset($XML->channel->language))
		{
			$language = (string) $XML->channel->language;
			$RSS->setLanguage($language);
		}

		// set last build date if it is present
		if(isset($XML->channel->lastBuildDate))
		{
			$lastBuildDate = (int) strtotime($XML->channel->lastBuildDate);
			$RSS->setLastBuildDate($lastBuildDate);
		}

		// set managing editor
		if(isset($XML->channel->managingEditor))
		{
			$managingEditor = (string) $XML->channel->managingEditor;
			$RSS->setManagingEditor($managingEditor);
		}

		// set publication date
		if(isset($XML->channel->pubDate))
		{
			$publicationDate = (int) strtotime($XML->channel->pubDate);
			$RSS->setPublicationDate($publicationDate);
		}

		// set rating
		if(isset($XML->channel->rating))
		{
			$rating = (string) $XML->channel->rating;
			$RSS->setRating($rating);
		}

		// set ttl
		if(isset($XML->channel->ttl))
		{
			$ttl = (int) $XML->channel->ttl;
			$RSS->setTTL($ttl);
		}

		// set webmaster
		if(isset($XML->channel->webmaster))
		{
			$webmaster = (string) $XML->channel->webmaster;
			$RSS->setWebmaster($webmaster);
		}

		// return
		return $RSS;
	}


	/**
	 * Set the charset.
	 *
	 * @param	string[optional] $charset	The charset that should be used. Possible charsets can be found in spoon.php.
	 */
	public function setCharset($charset = 'utf-8')
	{
		$this->charset = SpoonFilter::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET);
	}


	/**
	 * Set the cloud for the feed.
	 *
	 * @param	string $domain				The domain.
	 * @param	int $port					The port.
	 * @param	string $path				The path.
	 * @param	string $registerProcedure	The procedure.
	 * @param	string $protocol			The protocol to use. Possible values are: xml-rpc, soap, http-post.
	 */
	public function setCloud($domain, $port, $path, $registerProcedure, $protocol)
	{
		$this->cloud['domain'] = (string) $domain;
		$this->cloud['port'] = (int) $port;
		$this->cloud['path'] = (string) $path;
		$this->cloud['register_procedure'] = (string) $registerProcedure;
		$this->cloud['protocol'] = (string) SpoonFilter::getValue($protocol, array('xml-rpc', 'soap', 'http-post'), 'xml-rpc');
	}


	/**
	 * Set the copyright.
	 *
	 * @param	string $copyright	The copyright statement.
	 */
	public function setCopyright($copyright)
	{
		$this->copyright = (string) $copyright;
	}


	/**
	 * Set the description for the feed.
	 *
	 * @param	string $description		The description.
	 */
	public function setDescription($description)
	{
		$this->description = (string) $description;
	}


	/**
	 * Set the doc for the feed.
	 *
	 * @param	string $docs	The documentation statement.
	 */
	public function setDocs($docs)
	{
		$this->docs = (string) $docs;
	}


	/**
	 * Set the generator for the feed.
	 *
	 * @param	string[optional] $generator		The generator of the feed, if not given "Spoon/<SpoonVersion>" will be used.
	 */
	public function setGenerator($generator = null)
	{
		$this->generator = ($generator == null) ? 'Spoon/' . SPOON_VERSION : (string) $generator;
	}


	/**
	 * Set the image for the feed.
	 *
	 * @param	string $URL						URL of the image.
	 * @param	string $title					Title of the image.
	 * @param	string $link					Link of the image.
	 * @param	int[optional] $width			Width of the image.
	 * @param	int[optional] $height			Height of the image.
	 * @param	string[optional] $description	Description of the image.
	 */
	public function setImage($URL, $title, $link, $width = null, $height = null, $description = null)
	{
		// redefine vars
		$URL = (string) $URL;
		$link = (string) $link;

		// validate
		if(!SpoonFilter::isURL($URL)) throw new SpoonFeedException('This (' . $URL . ')isn\'t a valid URL.');
		if(!SpoonFilter::isURL($link)) throw new SpoonFeedException('This (' . $link . ') isn\'t a valid link.');

		// set properties
		$this->image['url'] = $URL;
		$this->image['title'] = (string) $title;
		$this->image['link'] = $link;
		if($width) $this->image['width'] = (int) $width;
		if($height) $this->image['height'] = (int) $height;
		if($description) $this->image['description'] = (string) $description;
	}


	/**
	 * Set the language for the feed.
	 *
	 * @param	string $language	The language to set.
	 */
	public function setLanguage($language)
	{
		$this->language = (string) $language;
	}


	/**
	 * Set the last build date for the feed.
	 *
	 * @param	int[optional] $lastBuildDate	A UNIX timestamp that represents the last build date.
	 */
	public function setLastBuildDate($lastBuildDate = null)
	{
		$this->lastBuildDate = ($lastBuildDate !== null) ? (int) $lastBuildDate : time();
	}


	/**
	 * Set the link for the feed.
	 *
	 * @param	string $link	The link of the feed.
	 */
	public function setLink($link)
	{
		// redefine vars
		$link = (string) $link;

		// validate
		if(!SpoonFilter::isURL($link)) throw new SpoonFeedException('This (' . $link . ') isn\'t a valid link');

		// set property
		$this->link = $link;
	}


	/**
	 * Set the managing editor for the feed.
	 *
	 * @param	string $managingEditor	The managing editor value.
	 */
	public function setManagingEditor($managingEditor)
	{
		$this->managingEditor = (string) $managingEditor;
	}


	/**
	 * Sets the publication date for the feed.
	 *
	 * @param	int[optional] $publicationDate		A UNIX timestamp that represents the publication date.
	 */
	public function setPublicationDate($publicationDate = null)
	{
		if($publicationDate) $publicationDate = time();
		$this->publicationDate = (int) $publicationDate;
	}


	/**
	 * Sets the rating.
	 *
	 * @param	string $rating		The rating for the feed.
	 */
	public function setRating($rating)
	{
		$this->rating = (string) $rating;
	}


	/**
	 * Set sorting status.
	 *
	 * @param	bool[optional] $on		Should the post be sorted?
	 */
	public function setSorting($on = true)
	{
		$this->sort = (bool) $on;
	}


	/**
	 * Set the sorting method.
	 *
	 * @param	string[optional] $sortingMethod		Set the sorting method that should be used, possible values are: desc, asc.
	 */
	public function setSortingMethod($sortingMethod = 'desc')
	{
		$aAllowedSortingMethods = array('asc', 'desc');

		// set sorting method
		self::$sortingMethod = SpoonFilter::getValue($sortingMethod, $aAllowedSortingMethods, 'desc');
	}


	/**
	 * Set the title for the feed.
	 *
	 * @param	string $title	The title of the feed.
	 */
	public function setTitle($title)
	{
		$this->title = (string) $title;
	}


	/**
	 * Set time to live for the feed.
	 *
	 * @param	int $ttl	The time to live in seconds.
	 */
	public function setTTL($ttl)
	{
		$this->ttl = (int) $ttl;
	}


	/**
	 * Sets the webmaster for the feed.
	 *
	 * @param	string $webmaster	The webmaster of the feed.
	 */
	public function setWebmaster($webmaster)
	{
		$this->webmaster = (string) $webmaster;
	}


	/**
	 * Sort the item on publication date.
	 */
	private function sort()
	{
		// get items
		$items = $this->getItems();

		// sort
		uasort($items, array('SpoonFeedRSS', 'compareObjects'));

		// set items
		$this->items = $items;
	}
}
