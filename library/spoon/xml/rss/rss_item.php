<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			xml
 * @subpackage		rss
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** Spoon RSS execption class */
require_once 'spoon/xml/rss/exception.php';

/** Spoon Filter class */
require_once 'spoon/filter/filter.php';


/**
 * This base class provides all the methods used by RSS-files
 *
 * @package			xml
 * @subpackage		rss
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */
final class SpoonRSSItem
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

		if($domain != null)
		{
			// add domain element
			$category['domain'] = (string) $domain;
		}

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
	public static function isValid($item)
	{
		// validate
		if(get_class($item) !== 'SimpleXMLElement') throw new SpoonRSSException('This isn\'t a valid object. Only SimpleXMLElement is allowed.');

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
	public static function readFromXML($item)
	{
		// validate
		if(get_class($item) != 'SimpleXMLElement') throw new SpoonRSSException('This isn\'t a valid object. Only SimpleXMLElement is allowed.');

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