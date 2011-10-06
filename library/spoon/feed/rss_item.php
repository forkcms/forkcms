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
 * This base class provides all the methods used by RSS-items.
 *
 * @package		spoon
 * @subpackage	feed
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.1.0
 */
class SpoonFeedRSSItem
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
	 * Default constructor.
	 *
	 * @param	string $title			The title for the item.
	 * @param	string $link			The link for the item.
	 * @param	string $description		The content for the item.
	 */
	public function __construct($title, $link, $description)
	{
		// set properties
		$this->setTitle($title);
		$this->setLink($link);
		$this->setDescription($description);
	}


	/**
	 * Add a category for the item.
	 *
	 * @param	string $name				The name of the category.
	 * @param	string[optional] $domain	The domain of the category.
	 */
	public function addCategory($name, $domain = null)
	{
		// create array
		$category['name'] = (string) $name;

		// has a domain
		if($domain != null) $category['domain'] = (string) $domain;

		// add property
		$this->categories[] = $category;
	}


	/**
	 * Builds the XML.
	 *
	 * @return	string		The fully build XML for an item.
	 */
	public function buildXML()
	{
		// init xmlstring
		$XML = '<item>' . "\n";

		// insert title
		$XML .= '	<title><![CDATA[' . $this->getTitle() . ']]></title>' . "\n";

		// insert link
		$XML .= '	<link>' . $this->getLink() . '</link>' . "\n";

		// insert description
		$XML .= '	<description>' . "\n";
		$XML .= '		<![CDATA[' . "\n";
		$XML .= '			' . $this->getDescription() . "\n";
		$XML .= '		]]>' . "\n";
		$XML .= '	</description>' . "\n";

		// insert item publication date
		$publicationDate = $this->getPublicationDate();
		if($publicationDate != '') $XML .= '	<pubDate>' . date('r', $publicationDate) . '</pubDate>' . "\n";

		// insert author
		$author = $this->getAuthor();
		if($author != '') $XML .= '	<author><![CDATA[' . $author . ']]></author>' . "\n";

		// insert source
		$source = $this->getSource();
		if(!empty($source))
		{
			$XML .= '	<source url="' . $source['url'] . '"><![CDATA[' . $source['name'] . ']]></source>' . "\n";
		}

		// insert categories
		$categories = $this->getCategories();
		if(!empty($categories))
		{
			foreach($categories as $category)
			{
				if(isset($category['domain'])) $XML .= '	<category domain="' . $category['domain'] . '"><![CDATA[' . $category['name'] . ']]></category>' . "\n";
				else $XML .= '	<category><![CDATA[' . $category['name'] . ']]></category>' . "\n";
			}
		}

		// insert guid
		$guid = $this->getGuid();
		if(!empty($guid))
		{
			// reformat
			$isPermaLink = ($guid['isPermaLink']) ? 'true' : 'false';

			// build xml
			$XML .= '	<guid isPermaLink="' . $isPermaLink . '">' . $guid['url'] . '</guid>' . "\n";
		}

		// insert enclosure
		$enclosure = $this->getEnclosure();
		if(!empty($enclosure))
		{
			if(isset($enclosure['url']) && isset($enclosure['length']) && isset($enclosure['type'])) $XML .= '	<enclosure url="' . $enclosure['url'] . '" length="' . $enclosure['length'] . '" type="' . $enclosure['type'] . '" />' . "\n";
		}

		// insert comments
		$commentsLink = $this->getCommentsLink();
		if($commentsLink != '') $XML .= '	<comments>' . $commentsLink . '</comments>' . "\n";

		// close item
		$XML .= '	</item>' . "\n";

		// return
		return $XML;
	}


	/**
	 * Get the author.
	 *
	 * @return	string
	 */
	public function getAuthor()
	{
		return $this->author;
	}


	/**
	 * Get the categories.
	 *
	 * @return	array	An array with all categories.
	 */
	public function getCategories()
	{
		return $this->categories;
	}


	/**
	 * Get the comment link.
	 *
	 * @return	string
	 */
	public function getCommentsLink()
	{
		return $this->commentsLink;
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
	 * Get the enclosure properties.
	 *
	 * @return	array
	 */
	public function getEnclosure()
	{
		return $this->enclosure;
	}


	/**
	 * Get the guid properties.
	 *
	 * @return	array
	 */
	public function getGuid()
	{
		return $this->guid;
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
	 * Get the publication date.
	 *
	 * @return	int
	 */
	public function getPublicationDate()
	{
		return $this->publicationDate;
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
	 * Get the source properties.
	 *
	 * @return	array
	 */
	public function getSource()
	{
		return $this->source;
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
	 * Validate if the given XML is valid.
	 *
	 * @return	bool						True if the item is valid, otherwise false.
	 * @param	SimpleXMLElement $item		An item/article from the whole feed.
	 */
	public static function isValid(SimpleXMLElement $item)
	{
		// are all needed elements present?
		if(!isset($item->title) || !isset($item->link) || !isset($item->description)) return false;

		// fallback
		return true;
	}


	/**
	 * Parse the item
	 *
	 * @return	string	The XML for the item
	 */
	public function parse()
	{
		return $this->buildXML();
	}


	/**
	 * Read an item from a SimpleXMLElement.
	 *
	 * @return	SpoonRSSItem				An instance of SpoonRSS.
	 * @param	SimpleXMLElement $item		The XML-element that represents a single item in the feed.
	 */
	public static function readFromXML(SimpleXMLElement $item)
	{
		// get title, link and description
		$title = (string) $item->title;
		$link = (string) $item->link;
		$description = (string) $item->description;

		// create instance
		$rssItem = new SpoonFeedRSSItem($title, $link, $description);

		// add categories
		if(isset($item->category))
		{
			foreach($item->category as $category)
			{
				// set property
				$rssItem->addCategory((string) $category, $category['domain']);
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
			$URL = (string) $item->enclosure['url'];
			$length = (int) $item->enclosure['length'];
			$type = (string) $item->enclosure['type'];

			// try to set enclosure
			try
			{
				// set enclosure
				$rssItem->setEnclosure($URL, $length, $type);
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
			$URL = (string) $item->guid;
			$isPermaLink = (bool) $item->guid['isPermaLink'];

			// try to set GUID
			try
			{
				// set GUID
				$rssItem->setGuid($URL, $isPermaLink);
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
			$URL = (string) $item->source['url'];

			// try to set source
			try
			{
				// set source
				$rssItem->setSource($name, $URL);
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
	 * Set the author.
	 *
	 * @param	string $author	The author of the item.
	 */
	public function setAuthor($author)
	{
		$this->author = (string) $author;
	}


	/**
	 * Set the comments link.
	 *
	 * @param	string $link	The link where the comments are available.
	 */
	public function setCommentsLink($link)
	{
		// redefine var
		$link = (string) $link;

		// validate
		if(!SpoonFilter::isURL($link)) throw new SpoonFeedException('This (' . $link . ') isn\'t a valid comments link.');

		// set property
		$this->commentsLink = $link;
	}


	/**
	 * Set the description.
	 *
	 * @param	string $description		The content of the item.
	 */
	public function setDescription($description)
	{
		$this->description = (string) $description;
	}


	/**
	 * Set the enclosure
	 *
	 * @param	string $URL		The URL of the enclosure.
	 * @param	int $length		The length of the enclosure.
	 * @param	string $type	The content-type of the enclosure.
	 */
	public function setEnclosure($URL, $length, $type)
	{
		// redefine var
		$URL = (string) $URL;

		// validate
		if(!SpoonFilter::isURL($URL)) throw new SpoonFeedException('This (' . $URL . ') isn\'t a valid URL for an enclosure.');

		// create array
		$enclosure['url'] = $URL;
		$enclosure['length'] = (int) $length;
		$enclosure['type'] = (string) $type;

		// set property
		$this->enclosure = $enclosure;
	}


	/**
	 * Set the guid.
	 *
	 * @param	string $URL						The URL of the item.
	 * @param	bool[optional] $isPermaLink		Is this the permalink?
	 */
	public function setGuid($URL, $isPermaLink = true)
	{
		// redefine var
		$URL = (string) $URL;

		// validate
		if(!SpoonFilter::isURL($URL)) throw new SpoonFeedException('This (' . $URL . ') isn\t a valid URL for guid.');

		// create array
		$guid['url'] = $URL;
		$guid['isPermaLink'] = (bool) $isPermaLink;

		// set property
		$this->guid = $guid;
	}


	/**
	 * Set the link.
	 *
	 * @param	string $link	The link of the item.
	 */
	public function setLink($link)
	{
		// redefine var
		$link = (string) $link;

		// validate
		if(!SpoonFilter::isURL($link)) throw new SpoonFeedException('This (' . $link . ') isn\'t a valid link.');

		// set property
		$this->link = $link;
	}


	/**
	 * Set the publication date.
	 *
	 * @param	int $publicationDate	The publication date as a UNIX-timestamp.
	 */
	public function setPublicationDate($publicationDate)
	{
		$this->publicationDate = (int) $publicationDate;
	}


	/**
	 * Set source.
	 *
	 * @param	string $name	The name of the source.
	 * @param	string $URL		The URL of the source.
	 */
	public function setSource($name, $URL)
	{
		// redefine var
		$URL = (string) $URL;

		// validate
		if(!SpoonFilter::isURL($URL)) throw new SpoonFeedException('This (' . $URL . ') isn\'t a valid URL for a source.');

		// create array
		$source['name'] = (string) $name;
		$source['url'] = $URL;

		// set property
		$this->source = $source;
	}


	/**
	 * Set the title.
	 *
	 * @param	string $title	The title of the item.
	 */
	public function setTitle($title)
	{
		$this->title = (string) $title;
	}
}
