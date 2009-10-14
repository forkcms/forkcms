<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		feed
 * @subpackage	rss
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		1.1.0
 */


/** Spoon File class */
require_once 'spoon/filesystem/filesystem.php';

/** Spoon HTTP class */
require_once 'spoon/http/http.php';


/**
 * This exception is used to handle rss related exceptions.
 *
 * @package		feed
 * @subpackage	rss
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @since		1.1.0
 */
class SpoonRSSException extends SpoonException {}


/**
 * This base class provides all the methods used by RSS-files
 *
 * @package		feed
 * @subpackage	rss
 *
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @since		1.1.0
 */
class SpoonRSS
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
	 * The sortingmethod
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
	 * @return	void
	 * @param	string $title
	 * @param	string $link
	 * @param	string $description
	 * @param	array[optional] $items
	 */
	public function __construct($title, $link, $description, $items = array())
	{
		// set properties
		$this->setTitle($title);
		$this->setLink($link);
		$this->setDescription($description);

		// loop items and add them
		foreach ($items as $item) $this->addItem($item);
	}


	/**
	 * Adds a category for the feed
	 *
	 * @return	void
	 * @param	string $category
	 * @param	string[optional] $domain
	 */
	public function addCategory($category, $domain = null)
	{
		// build array
		$aCategory['category'] = (string) $category;
		if($domain) $aCategory['domain'] = (string) $domain;

		// set property
		$this->categories[] = $aCategory;
	}


	/**
	 * Add an item to the feed
	 *
	 * @return	void
	 * @param	SpoonRSSItem $item
	 */
	public function addItem(SpoonRSSItem $item)
	{
		$this->items[] = $item;
	}


	/**
	 * Add a day to skip. the default value is sunday
	 *
	 * @return	void
	 * @param	string $day
	 */
	public function addSkipDay($day)
	{
		$aAllowedDays = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saterday');

		// redefine var
		$day = (string) SpoonFilter::getValue(strtolower($day), $aAllowedDays, 'sunday');

		// validate
		if(in_array($day, $this->skipDays)) throw new SpoonRSSException('This ('. $day .') day is already added.');

		// set property
		$this->skipDays[] = ucfirst($day);
	}


	/**
	 * Add a hour to skip, default is 0
	 *
	 * @return	void
	 * @param 	int $hour
	 */
	public function addSkipHour($hour)
	{
		// allowed hours
		$aAllowedHours = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23);

		// redefine var
		$hour = (int) SpoonFilter::getValue($hour, $aAllowedHours, 0);

		// validate
		if(!in_array($hour, $aAllowedHours)) throw new SpoonRSSException('This ('. $hour .') isn\'t a valid hour. Only '. join(', ', $aAllowedHours) .' are allowed.)');
		if(in_array($hour, $this->skipHours)) throw new SpoonRSSException('This ('. $hour .') hour is already added.');

		// set property
		$this->skipHours[] = (int) $hour;
	}


	/**
	 * Build the xmlfile
	 *
	 * @return	string
	 */
	private function buildXML()
	{
		// sort if needed
		if($this->getSorting()) $this->sort();

		// init xml
		$xml = '<?xml version="1.0" encoding="'. strtolower($this->getCharset()) .'" ?>'."\n";
		$xml .= '<rss version="2.0">'."\n";
		$xml .= '<channel>'."\n";

		// insert title
		$xml .= '	<title>'. $this->getTitle() .'</title>'."\n";

		// insert link
		$xml .= '	<link>'. $this->getLink() .'</link>'."\n";

		// insert description
		$xml .= '	<description>'."\n";
		$xml .= '		<![CDATA['."\n";
		$xml .= '		'. $this->getDescription() ."\n";
		$xml .= '		]]>'."\n";
		$xml .= '</description>'."\n";

		// insert image if needed
		$imageProperties = $this->getImage();
		if(!empty($imageProperties))
		{
			$image = $this->getImage();

			$xml .= '	<image>'."\n";
			$xml .= '		<title>'. $image['title'] .'</title>'."\n";
			$xml .= '		<url>'. $image['url'] .'</url>'."\n";
			$xml .= '		<link>'. $image['link'] .'</link>'."\n";
			if(isset($image['width']) && $image['width'] != '') $xml .= '		<width>'. $image['title'] .'</width>'."\n";
			if(isset($image['height']) && $image['height'] != '') $xml .= '		<height>'. $image['title'] .'</height>'."\n";
			if(isset($image['description']) && $image['description'] != '') $xml .= '		<description><![CDATA['. $image['title'] .']]></description>'."\n";
			$xml .= '	</image>'."\n";
		}

		// insert last build date
		if($this->getLastBuildDate() != '') $xml .= '	<lastBuildDate>'. $this->getLastBuildDate('r') .'</lastBuildDate>'."\n";

		// insert publication date
		if($this->getPublicationDate() != '') $xml .= '	<pubDate>'. $this->getPublicationDate('r') .'</pubDate>'."\n";

		// insert time to live
		if($this->getTTL() != '') $xml .= '	<ttl>'. $this->getTTL() .'</ttl>'."\n";

		// insert managing editor
		if($this->getManagingEditor() != '') $xml .= '	<managingEditor>'. $this->getManagingEditor() .'</managingEditor>>'."\n";

		// insert webmaster
		if($this->getWebmaster() != '') $xml .= '	<webmaster>'. $this->getWebmaster() .'></webmaster>'."\n";

		// insert copyright
		if($this->getCopyright() != '') $xml .= '	<copyright>'. $this->getCopyright() .'</copyright>'."\n";

		// insert categories
		$categories = $this->getCategories();
		if(!empty($categories))
		{
			foreach ($this->getCategories() as $category)
			{
				if(isset($category['domain']) && $category['domain'] != '') $xml .= '	<category domain="'. $category['domain'] .'"><![CDATA['. $category['category'] .']]></category>'."\n";
				else $xml .= '	<category><![CDATA['. $category['category'] .']]</category>'."\n";
			}
		}

		// insert rating
		if($this->getRating() != '') $xml .= '	<rating>'. $this->getRating() .'</rating>'."\n";

		// insert generator
		if($this->getGenerator() != '') $xml .= '	<generator><![CDATA['. $this->getGenerator() .']]></generator>'."\n";

		// insert language
		if($this->getLanguage() != '') $xml .= '	<language>'. $this->getLanguage() .'</language>'."\n";

		// insert docs
		if($this->getDocs() != '') $xml .= '	<docs>'. $this->getDocs() .'</docs>'."\n";

		// insert skipdays
		$skipDays = $this->getSkipDays();
		if(!empty($skipDays))
		{
			$xml .= '	<skipDays>'."\n";
			foreach ($skipDays as $day) $xml .= '	<day>'.$day.'</day>'."\n";
			$xml .= '	</skipDays>'."\n";
		}

		// insert skiphours
		$skipHours = $this->getSkipHours();
		if(!empty($skipHours))
		{
			$xml .= '	<skipHours>'."\n";
			foreach ($skipHours as $hour) 			$xml .= '	<hour>'.$hour.'</hour>'."\n";
			$xml .= '	</skipHours>'."\n";
		}

		// insert cloud
		$cloudProperties = $this->getCloud();
		if(!empty($cloudProperties))
		{
			$cloud = $this->getCloud();
			$xml .= '	<cloud domain="'. $cloud['domain'] .'" port="'. $cloud['port'] .'" path="'. $cloud['path'] .'" registerProce-dure="'. $cloud['register_procedure'] .'" protocol="'. $cloud['protocol'] .'" />'."\n";
		}

		// insert items
		foreach ($this->getItems() as $item)
		{
			$xml .= $item->parse();
		}

		// add endtags
		$xml .= '</channel>'."\n";
		$xml .= '</rss>'."\n";

		return $xml;
	}


	/**
	 * Compare Objects for sorting on publication date
	 *
	 * @return	int
	 * @param	SpoonRSSItem $object1
	 * @param	SpoonRSSItem $object2
	 * @param	string[optional] $sortingMethod
	 */
	private static function compareObjects(SpoonRSSItem $object1, SpoonRSSItem $object2)
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
	 * Retrieves the categories for a feed
	 *
	 * @return	void
	 */
	public function getCategories()
	{
		return $this->categories;
	}


	/**
	 * Get the charset
	 *
	 * @return	string
	 */
	public function getCharset()
	{
		return $this->charset;
	}


	/**
	 * Get the cloud
	 *
	 * @return	array
	 */
	public function getCloud()
	{
		return $this->cloud;
	}


	/**
	 * Get the copyright
	 *
	 * @return	string
	 */
	public function getCopyright()
	{
		return $this->copyright;
	}


	/**
	 * Get the description
	 *
	 * @return	string
	 */
	public function getDescription()
	{
		return $this->description;
	}


	/**
	 * Get the docs
	 *
	 * @return	string
	 */
	public function getDocs()
	{
		return $this->docs;
	}


	/**
	 * Get the generator
	 *
	 * @return	string
	 */
	public function getGenerator()
	{
		return $this->generator;
	}


	/**
	 * Retrieves the image properties
	 *
	 * @return	array
	 */
	public function getImage()
	{
		return $this->image;
	}


	/**
	 * Retrieves the items
	 *
	 * @return	array
	 */
	public function getItems()
	{
		return $this->items;
	}


	/**
	 * Get the language
	 *
	 * @return	string
	 */
	public function getLanguage()
	{
		return $this->language;
	}


	/**
	 * Get the last build date
	 *
	 * @return	mixed
	 * @param	string[optional] $format
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
	 * Get the link
	 *
	 * @return	string
	 */
	public function getLink()
	{
		return $this->link;
	}


	/**
	 * Get the managing editor
	 *
	 * @return	string
	 */
	public function getManagingEditor()
	{
		return $this->managingEditor;
	}


	/**
	 * Get the publication date
	 *
	 * @return	mixed
	 * @param	string[optional] format
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
	 * Get the raw XML
	 *
	 * @return	string
	 */
	public function getRawXML()
	{
		return $this->buildXML();
	}


	/**
	 * Retrieves the days to skip
	 *
	 * @return	array
	 */
	public function getSkipDays()
	{
		return $this->skipDays;
	}


	/**
	 * Retrieves the hours to skip
	 *
	 * @return	array
	 */
	public function getSkipHours()
	{
		return $this->skipHours;
	}


	/**
	 * Get sorting status
	 *
	 * @return	bool
	 */
	public function getSorting()
	{
		return $this->sort;
	}


	/**
	 * Get the sorting method
	 *
	 * @return	string
	 */
	public function getSortingMethod()
	{
		return self::$sortingMethod;
	}


	/**
	 * Get the title
	 *
	 * @return	array
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * Get the time to life
	 *
	 * @return	int
	 */
	public function getTTL()
	{
		return $this->ttl;
	}


	/**
	 * Get the webmaster
	 *
	 * @return	string
	 */
	public function getWebmaster()
	{
		return $this->webmaster;
	}


	/**
	 * Checks if the feed is valid
	 *
	 * @return	bool
	 * @param	string $url
	 * @param	string[optional] $type
	 */
	public static function isValid($url, $type = 'url')
	{
		// redefine var
		$url = (string) $url;
		$type = (string) SpoonFilter::getValue($type, array('url', 'string'), 'url');

		// validate
		if($type == 'url' && !SpoonFilter::isURL($url)) throw new SpoonRSSException('This ('. $url .') isn\'t a valid url.');

		// load xmlstring
		if($type == 'url')
		{
			// check if allow_url_fopen is enabled
			if(ini_get('allow_url_fopen') == 0) throw new SpoonRSSException('allow_url_fopen should be enabled, if you want to get a remote url.');

			// open the url
			$handle = @fopen($url, 'r');

			// validate the handle
			if($handle === false) throw new SpoonRSSException('Something went wrong while retrieving the url.');

			// read the string
			$xmlString = @stream_get_contents($handle);

			// close the hanlde
			@fclose($handle);
		}

		// not that url
		else $xmlString = $url;

		// convert to simpleXML
		$xml = @simplexml_load_string($xmlString);

		// invalid XML?
		if($xml === false) return false;

		// check if all needed elements are present
		if(!isset($xml->channel) || !isset($xml->channel->title) || !isset($xml->channel->link) || !isset($xml->channel->description) || !isset($xml->channel->item)) return false;

		// loop items
		foreach ($xml->channel->item as $item)
		{
			// validate items
			if(!SpoonRSSItem::isValid($item)) return false;
		}

		// fallback
		return true;
	}


	/**
	 * Parse the feed and output the feed into the browser
	 *
	 * @return	void
	 * @param	bool[optional] $headers
	 */
	public function parse($headers = true)
	{
		// set headers
		if($headers) SpoonHTTP::setHeaders(self::HEADER . $this->getCharset());

		// output
		echo $this->buildXML();

		// stop here
		exit;
	}


	/**
	 * Write the feed into a file
	 *
	 * @return	void
	 * @param	string $path
	 */
	public function parseToFile($path)
	{
		// get xml
		$xml = $this->buildXML();

		// write content
		SpoonFile::setFileContent((string) $path, $xml, false, true);
	}


	/**
	 * Reads an feed into a SpoonRSS object
	 *
	 * @return	SpoonRSS
	 * @param	string $url
	 * @param	string[optional] $type
	 */
	public static function readFromFeed($url, $type = 'url')
	{
		// redefine var
		$url = (string) $url;
		$type = (string) SpoonFilter::getValue($type, array('url', 'string'), 'url');

		// validate
		if($type == 'url' && !SpoonFilter::isURL($url)) throw new SpoonRSSException('This ('. SpoonFilter::htmlentities($url) .') isn\'t a valid url.');
		if(!self::isValid($url, $type)) throw new SpoonRSSException('Invalid feed');

		// load xmlstring
		if($type == 'url')
		{
			// check if allow_url_fopen is enabled
			if(ini_get('allow_url_fopen') == 0) throw new SpoonRSSException('allow_url_fopen should be enabled, if you want to get a remote url.');

			// open the url
			$handle = @fopen($url, 'r');

			// validate the handle
			if($handle === false) throw new SpoonRSSException('Something went wrong while retrieving the url.');

			// read the string
			$xmlString = @stream_get_contents($handle);

			// close the hanlde
			@fclose($handle);
		}

		// not that url
		else $xmlString = $url;

		// convert to simpleXML
		$xml = @simplexml_load_string($xmlString);

		// validate the feed
		if($xml === false) throw new SpoonRSSException('Invalid rss-string.');

		// get title, link and description
		$title = (string) $xml->channel->title;
		$link = (string) $xml->channel->link;
		$description = (string) $xml->channel->description;

		// create instance
		$rss = new SpoonRSS($title, $link, $description);

		// add items
		foreach($xml->channel->item as $item)
		{
			// try to read
			try
			{
				// read xml
				$item = SpoonRSSItem::readFromXML($item);
				$rss->addItem($item);
			}

			// catch exceptions
			catch (Exception $e)
			{
				// ignore exceptions
			}
		}

		// add category
		if(isset($xml->channel->category))
		{
			foreach ($xml->channel->category as $category)
			{
				if(isset($category['domain'])) $rss->addCategory((string) $category, (string) $category['domain']);
				else $rss->addCategory((string) $category);
			}
		}

		// add skip day
		if(isset($xml->channel->skipDays))
		{
			// loop ski-days
			foreach ($xml->channel->skipDays->day as $day)
			{
				// try to add
				try
				{
					// add skip-day
					$rss->addSkipDay((string) $day);
				}

				// catch exception
				catch (Exception $e)
				{
					// ignore exceptions
				}
			}
		}

		// add skip hour
		if(isset($xml->channel->skipHours))
		{
			foreach ($xml->channel->skipHours->hour as $hour)
			{
				// try to add
				try
				{
					// add skip hour
					$rss->addSkipHour((int) $hour);
				}

				// catch exception
				catch (Exception $e)
				{
					// ignore exceptions
				}
			}
		}

		// set cloud
		if(isset($xml->channel->cloud['domain']) && isset($xml->channel->cloud['port']) && isset($xml->channel->cloud['path']) && isset($xml->channel->cloud['registerProce-dure']) && isset($xml->channel->cloud['protocol']))
		{
			// read attributes
			$cloudDomain = (string) $xml->channel->cloud['domain'];
			$cloudPort = (int) $xml->channel->cloud['port'];
			$cloudPath = (string) $xml->channel->cloud['path'];
			$cloudRegisterProcedure = (string) $xml->channel->cloud['registerProce-dure'];
			$cloudProtocol = (string) $xml->channel->cloud['protocol'];

			// set property
			$rss->setCloud($cloudDomain, $cloudPort, $cloudPath, $cloudRegisterProcedure, $cloudProtocol);
		}

		// set copyright
		if(isset($xml->channel->copyright))
		{
			$copyright = (string) $xml->channel->copyright;
			$rss->setCopyright($copyright);
		}

		// set docs
		if(isset($xml->channel->docs))
		{
			$docs = (string) $xml->channel->docs;
			$rss->setDocs($docs);
		}

		// set generator if it is present
		if(isset($xml->channel->generator))
		{
			$generator = (string) $xml->channel->generator;
			$rss->setGenerator($generator);
		}

		// set image if it is present
		if(isset($xml->channel->image->title) && isset($xml->channel->image->url) && isset($xml->channel->image->link))
		{
			// read properties
			$imageTitle = (string) $xml->channel->image->title;
			$imageUrl = (string) $xml->channel->image->url;
			$imageLink = (string) $xml->channel->image->link;

			// read optional properties
			if(isset($xml->channel->image->width)) $imageWidth = (int) $xml->channel->image->width;
			else $imageWidth = null;
			if(isset($xml->channel->image->height)) $imageHeight = (int) $xml->channel->image->height;
			else $imageHeight = null;
			if(isset($xml->channel->image->description)) $imageDescription = (string) $xml->channel->image->description;
			else $imageDescription = null;

			// try to set image
			try
			{
				// set image
				$rss->setImage($imageUrl, $imageTitle, $imageLink, $imageWidth, $imageHeight, $imageDescription);
			}

			// catch exception
			catch (Exception $e)
			{
				// ignore exceptions
			}
		}

		// set language if its is present
		if(isset($xml->channel->language))
		{
			$language = (string) $xml->channel->language;
			$rss->setLanguage($language);
		}

		// set last build date if it is present
		if(isset($xml->channel->lastBuildDate))
		{
			$lastBuildDate = (int) strtotime($xml->channel->lastBuildDate);
			$rss->setLastBuildDate($lastBuildDate);
		}

		// set managing editor
		if(isset($xml->channel->managingEditor))
		{
			$managingEditor = (string) $xml->channel->managingEditor;
			$rss->setManagingEditor($managingEditor);
		}

		// set publication date
		if(isset($xml->channel->pubDate))
		{
			$publicationDate = (int) strtotime($xml->channel->pubDate);
			$rss->setPublicationDate($publicationDate);
		}

		// set rating
		if(isset($xml->channel->rating))
		{
			$rating = (string) $xml->channel->rating;
			$rss->setRating($rating);
		}

		// set ttl
		if(isset($xml->channel->ttl))
		{
			$ttl = (int) $xml->channel->ttl;
			$rss->setTTL($ttl);
		}

		// set webmaster
		if(isset($xml->channel->webmaster))
		{
			$webmaster = (string) $xml->channel->webmaster;
			$rss->setWebmaster($webmaster);
		}

		// return
		return $rss;
	}


	/**
	 * Set the charset
	 *
	 * @return	void
	 * @param	string[optional] $charset
	 */
	public function setCharset($charset = 'utf-8')
	{
		$this->charset = SpoonFilter::getValue($charset, Spoon::getCharsets(), SPOON_CHARSET);
	}


	/**
	 * Set the cloud for the feed
	 *
	 * @return	void
	 * @param	string $domain
	 * @param	int $port
	 * @param	string $path
	 * @param	string $registerProcedure
	 * @param	string $protocol
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
	 * Set the copyright
	 *
	 * @return	void
	 * @param	string $copyright
	 */
	public function setCopyright($copyright)
	{
		$this->copyright = (string) $copyright;
	}


	/**
	 * Set the description for the feed
	 *
	 * @return	void
	 * @param	string $description
	 */
	public function setDescription($description)
	{
		$this->description = (string) $description;
	}


	/**
	 * Set the doc for the feed
	 *
	 * @return	void
	 * @param	string $docs
	 */
	public function setDocs($docs)
	{
		$this->docs = (string) $docs;
	}


	/**
	 * Set the generator for the feed
	 *
	 * @return	void
	 * @param	string[optional] $generator
	 */
	public function setGenerator($generator = null)
	{
		$this->generator = ($generator == null) ? 'Spoon/'.SPOON_VERSION : (string) $generator;
	}


	/**
	 * Set the image for the feed
	 *
	 * @return	void
	 * @param	string $url
	 * @param	string $title
	 * @param	string $link
	 * @param	int[optional] $width
	 * @param	int[optional] $height
	 * @param	string[optional] $description
	 */
	public function setImage($url, $title, $link, $width = null, $height = null, $description = null)
	{
		// redefine vars
		$url = (string) $url;
		$link = (string) $link;

		// validate
		if(!SpoonFilter::isURL($url)) throw new SpoonRSSException('This ('. $url .')isn\'t a valid url.');
		if(!SpoonFilter::isURL($link)) throw new SpoonRSSException('This ('. $link .') isn\'t a valid link.');

		// set properties
		$this->image['url'] = $url;
		$this->image['title'] = (string) $title;
		$this->image['link'] = $link;
		if($width) $this->image['width'] = (int) $width;
		if($height) $this->image['height'] = (int) $height;
		if($description) $this->image['description'] = (string) $description;
	}


	/**
	 * Set the language for the feed
	 *
	 * @return	void
	 * @param	string $language
	 */
	public function setLanguage($language)
	{
		$this->language = (string) $language;
	}


	/**
	 * Set the last build date for the feed
	 *
	 * @return	void
	 * @param	int[optional] $lastBuildDate
	 */
	public function setLastBuildDate($lastBuildDate = null)
	{
		if($lastBuildDate) $lastBuildDate = time();
		$this->lastBuildDate = (int) $lastBuildDate;
	}


	/**
	 * Set the link for the feed
	 *
	 * @return	void
	 * @param	string $link
	 */
	public function setLink($link)
	{
		// redefine vars
		$link = (string) $link;

		// validate
		if(!SpoonFilter::isURL($link)) throw new SpoonRSSException('This ('. $link .') isn\'t a valid link');

		// set property
		$this->link = $link;
	}


	/**
	 * Set the managing editor for the feed
	 *
	 * @return	void
	 * @param	string $managingEditor
	 */
	public function setManagingEditor($managingEditor)
	{
		$this->managingEditor = (string) $managingEditor;
	}


	/**
	 * Sets the publication date for the feed
	 *
	 * @return	void
	 * @param	int[optional] $publicationDate
	 */
	public function setPublicationDate($publicationDate = null)
	{
		if($publicationDate) $publicationDate = time();
		$this->publicationDate = (int) $publicationDate;
	}


	/**
	 * Sets the rating
	 *
	 * @return	void
	 * @param	string $rating
	 */
	public function setRating($rating)
	{
		$this->rating = (string) $rating;
	}


	/**
	 * Set sorting status
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function setSorting($on = true)
	{
		$this->sort = (bool) $on;
	}


	/**
	 * Set the sorting method
	 *
	 * @return	void
	 * @param	string[optional] $sortingMethod
	 */
	public function setSortingMethod($sortingMethod = 'desc')
	{
		$aAllowedSortingMethods = array('asc', 'desc');

		// set sorting method
		self::$sortingMethod = SpoonFilter::getValue($sortingMethod, $aAllowedSortingMethods, 'desc');
	}


	/**
	 * Set the title for the feed
	 *
	 * @return	void
	 * @param	string $title
	 */
	public function setTitle($title)
	{
		$this->title = (string) $title;
	}


	/**
	 * Set time to live for the feed
	 *
	 * @return	void
	 * @param	int $ttl
	 */
	public function setTTL($ttl)
	{
		$this->ttl = (int) $ttl;
	}


	/**
	 * Sets the webmaster for the feed
	 *
	 * @return	void
	 * @param	string $webmaster
	 */
	public function setWebmaster($webmaster)
	{
		$this->webmaster = (string) $webmaster;
	}


	/**
	 * Sort the item on publication date
	 *
	 * @return	void
	 */
	private function sort()
	{
		// get items
		$items = $this->getItems();

		// sort
		uasort($items, array('SpoonRSS', 'compareObjects'));

		// set items
		$this->items = $items;
	}
}


/**
 * This base class provides all the methods used by RSS-files
 *
 * @package		feed
 * @subpackage	rss
 *
 *
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @since		1.1.0
 */
class SpoonRSSItem
{
	/**
	 * Specifies the author of the item
	 *
	 * @var	string
	 */
	private $author;


	/**
	 * Defines the categories the item belongs to
	 *
	 * @var	array
	 */
	private $categories = array();


	/**
	 * The link to the comments about that item
	 *
	 * @var	string
	 */
	private $commentsLink;


	/**
	 * Describes the item
	 *
	 * @var	string
	 */
	private $description;


	/**
	 * An included media file for the item
	 *
	 * @var	array
	 */
	private $enclosure;


	/**
	 * Defines a unique identifier for the item
	 *
	 * @var	array
	 */
	private $guid = array();


	/**
	 * Defines the hyperlink to the item
	 *
	 * @var	string
	 */
	private $link;


	/**
	 * Defines the last-publication date for the item
	 *
	 * @var	int
	 */
	private $publicationDate;


	/**
	 * Specifies a third-party source for the item
	 *
	 * @var	array
	 */
	private $source = array();


	/**
	 * Defines the title of the item
	 *
	 * @var	string
	 */
	private $title;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $title
	 * @param	string $link
	 * @param	string $description
	 */
	public function __construct($title, $link, $description)
	{
		// set properties
		$this->setTitle($title);
		$this->setLink($link);
		$this->setDescription($description);
	}


	/**
	 * Build the XML
	 *
	 * @return	void;
	 */
	public function buildXML()
	{
		// init xmlstring
		$xml = '<item>'."\n";

		// insert title
		$xml .= '	<title><![CDATA['. $this->getTitle() .']]></title>'."\n";

		// insert link
		$xml .= '	<link>'. $this->getLink() .'</link>'."\n";

		// insert description
		$xml .= '	<description>'."\n";
		$xml .= '		<![CDATA['."\n";
		$xml .= '			'. $this->getDescription() ."\n";
		$xml .= '		]]>'."\n";
		$xml .= '	</description>'."\n";

		// insert item publication date
		$publicationDate = $this->getPublicationDate();
		if($publicationDate != '') $xml .= '	<pubDate>'. date('r', $publicationDate) .'</pubDate>'."\n";

		// insert author
		$author = $this->getAuthor();
		if($author != '') $xml .= '	<author><![CDATA['. $author .']]></author>'."\n";

		// insert source
		$source = $this->getSource();
		if(!empty($source))
		{
			$xml .= '	<source url="'. $source['url'] .'"><![CDATA['. $source['name'] .']]></source>'."\n";
		}

		// insert categories
		$categories = $this->getCategories();
		if(!empty($categories))
		{
			foreach($categories as $category)
			{
				if(isset($category['domain'])) $xml .= '	<category domain="'. $category['domain'] .'"><![CDATA['. $category['name'] .']]></category>'."\n";
				else $xml .= '	<category><![CDATA['. $category['name'] .']]></category>'."\n";
			}
		}

		// insert guid
		$guid = $this->getGuid();
		if(!empty($guid))
		{
			$xml .= '	<guid isPermaLink="'. $guid['isPermaLink'] .'">'. $guid['url'] .'</guid>'."\n";
		}

		// insert enclosure
		$enclosure = $this->getEnclosure();
		if(!empty($enclosure))
		{
			if(isset($enclosure['url']) && isset($enclosure['length']) && isset($enclosure['type'])) $xml .= '	<enclosure url="'. $enclosure['url'] .'" length="'. $enclosure['length'] .'" type="'. $enclosure['type'] .'" />'."\n";
		}

		// insert comments
		$commentsLink = $this->getCommentsLink();
		if($commentsLink != '') $xml .= '	<comments>'. $commentsLink .'</comments>'."\n";

		$xml .= '	</item>'."\n";

		// return
		return $xml;
	}


	/**
	 * Add a category for the item
	 *
	 * @return	void
	 * @param	string $categoryName
	 * @param	string[optional] $domain
	 */
	public function addCategory($categoryName, $domain = null)
	{
		// create array
		$category['name'] = (string) $categoryName;

		// has a domain
		if($domain != null) $category['domain'] = (string) $domain;

		// add property
		$this->categories[] = $category;
	}


	/**
	 * Get the author
	 *
	 * @return	string
	 */
	public function getAuthor()
	{
		return $this->author;
	}


	/**
	 * Get the categories
	 *
	 * @return	array
	 */
	public function getCategories()
	{
		return $this->categories;
	}


	/**
	 * Get the comment link
	 *
	 * @return	string
	 */
	public function getCommentsLink()
	{
		return $this->commentsLink;
	}


	/**
	 * Get the description
	 *
	 * @return	string
	 */
	public function getDescription()
	{
		return $this->description;
	}


	/**
	 * Get the enclosure properties
	 *
	 * @return	array
	 */
	public function getEnclosure()
	{
		return $this->enclosure;
	}


	/**
	 * Get the guid properties
	 *
	 * @return	array
	 */
	public function getGuid()
	{
		return $this->guid;
	}


	/**
	 * Get the link
	 *
	 * @return	string
	 */
	public function getLink()
	{
		return $this->link;
	}


	/**
	 * Get the publication date
	 *
	 * @return	int
	 */
	public function getPublicationDate()
	{
		return $this->publicationDate;
	}


	/**
	 * Get the raw XML
	 *
	 * @return	string
	 */
	public function getRawXML()
	{
		return $this->buildXML();
	}


	/**
	 * Get the source properties
	 *
	 * @return	array
	 */
	public function getSource()
	{
		return $this->source;
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
	 * Validate if the given XML is valid
	 *
	 * @return	bool
	 * @param	SimpleXMLElement $item
	 */
	public static function isValid(SimpleXMLElement $item)
	{
		// are all needed elements present?
		if(!isset($item->title) || !isset($item->link) || !isset($item->description)) return false;

		// fallback
		return true;
	}


	/**
	 * Get the parse XML
	 *
	 * @return	string
	 */
	public function parse()
	{
		return $this->buildXML();
	}


	/**
	 * Read an item from a SimpleXMLElement
	 *
	 * @return	SpoonRSSItem
	 * @param	SimpleXMLElement $item
	 */
	public static function readFromXML(SimpleXMLElement $item)
	{
		// get title, link and description
		$title = (string) $item->title;
		$link = (string) $item->link;
		$description = (string) $item->description;

		// create instance
		$rssItem = new SpoonRSSItem($title, $link, $description);

		// add categories
		if(isset($item->category))
		{
			foreach($item->category as $category)
			{
				$categoryName = (string) $category;
				$domain = $category['domain'];

				// set property
				$rssItem->addCategory($categoryName, $domain);
			}
		}

		// set author
		if(isset($item->author)) $rssItem->setAuthor((string) $item->author);

		// set commentslink
		if(isset($item->comments))
		{
			// try to set the commentslink
			try
			{
				// set commentslink
				$rssItem->setCommentsLink((string) $item->comments);
			}

			// catch exceptions
			catch(Exception $e)
			{
				// ignore exceptions
			}
		}

		// set enclosure
		if(isset($item->enclosure['url']) && isset($item->enclosure['length']) && isset($item->enclosure['type']))
		{
			// read data
			$url = (string) $item->enclosure['url'];
			$length = (int) $item->enclosure['length'];
			$type = (string) $item->enclosure['type'];

			// try to set enclosure
			try
			{
				// set enclosure
				$rssItem->setEnclosure($url, $length, $type);
			}

			// catch exceptions
			catch(Exception $e)
			{
				// ignore exceptions
			}
		}

		// set guid
		if(isset($item->guid))
		{
			// read data
			$url = (string) $item->guid;
			$isPermaLink = (bool) $item->guid['isPermaLink'];

			// try to set GUID
			try
			{
				// set GUID
				$rssItem->setGuid($url, $isPermaLink);
			}

			// catch exceptions
			catch(Exception $e)
			{
				// ignore exceptions
			}
		}

		// set publication date
		if(isset($item->pubDate)) $rssItem->setPublicationDate((int) strtotime($item->pubDate));

		// set source
		if(isset($item->source))
		{
			// read data
			$name = (string) $item->source;
			$url = (string) $item->source['url'];

			// try to set source
			try
			{
				// set source
				$rssItem->setSource($name, $url);
			}

			// catch exceptions
			catch(Exception $e)
			{
				// ignore exceptions
			}
		}

		return $rssItem;
	}


	/**
	 * Set the author
	 *
	 * @return	void
	 * @param	string $author
	 */
	public function setAuthor($author)
	{
		$this->author = (string) $author;
	}


	/**
	 * Set the comment link
	 *
	 * @return	void
	 * @param	string $link
	 */
	public function setCommentsLink($link)
	{
		// redefine var
		$link = (string) $link;

		// validate
		if(!SpoonFilter::isURL($link)) throw new SpoonRSSException('This ('. $link .') isn\'t a valid comments link.');

		// set property
		$this->commentsLink = $link;
	}


	/**
	 * Set the description
	 *
	 * @return	void
	 * @param	string $description
	 */
	public function setDescription($description)
	{
		$this->description = (string) $description;
	}


	/**
	 * Set the enclosure
	 *
	 * @return	void
	 * @param	string $url
	 * @param	int $length
	 * @param	string $type
	 */
	public function setEnclosure($url, $length, $type)
	{
		// redefine var
		$url = (string) $url;

		// validate
		if(!SpoonFilter::isURL($url)) throw new SpoonRSSException('This ('. $url .') isn\'t a valid url for an enclosure.');

		// create array
		$enclosure['url'] = $url;
		$enclosure['length'] = (int) $length;
		$enclosure['type'] = (string) $type;

		// set property
		$this->enclosure = $enclosure;
	}


	/**
	 * Set the guid
	 *
	 * @return	void
	 * @param	string $url
	 * @param	bool[optional] $isPermaLink
	 */
	public function setGuid($url, $isPermaLink = true)
	{
		// reefine var
		$url = (string) $url;

		// validate
		if(!SpoonFilter::isURL($url)) throw new SpoonRSSException('This ('. $url .') isn\t a valid url for guid.');

		// create array
		$guid['url'] = $url;
		$guid['isPermaLink'] = (bool) $isPermaLink;

		// set property
		$this->guid = $guid;
	}


	/**
	 * Set the link
	 *
	 * @return	void
	 * @param	string $link
	 */
	public function setLink($link)
	{
		// redefine var
		$link = (string) $link;

		// validate
		if(!SpoonFilter::isURL($link)) throw new SpoonRSSException('This ('. $link .') isn\'t a valid link.');

		// set property
		$this->link = $link;
	}


	/**
	 * Set the publication date
	 *
	 * @return	void
	 * @param	int $publicationDate
	 */
	public function setPublicationDate($publicationDate)
	{
		$this->publicationDate = (int) $publicationDate;
	}


	/**
	 * Set source
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string $url
	 */
	public function setSource($name, $url)
	{
		// redefine var
		$url = (string) $url;

		// validate
		if(!SpoonFilter::isURL($url)) throw new SpoonRSSException('This ('. $url .') isn\'t a valid url for a source.');

		// create array
		$source['name'] = (string) $name;
		$source['url'] = $url;

		// set property
		$this->source = $source;
	}


	/**
	 * Set the title
	 *
	 * @return	void
	 * @param	string $title
	 */
	public function setTitle($title)
	{
		$this->title = (string) $title;
	}
}

?>
