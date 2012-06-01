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
 * This base class provides all the methods used by Atom RSS-files
 *
 * @package		spoon
 * @subpackage	feed
 *
 * @author		Lowie Benoot <lowiebenoot@netlash.com>
 * @since		1.1.0
 */
class SpoonFeedAtomRSS
{
	/**
	 * the authors (name, email, uri)
	 *
	 * @var	array
	 */
	private $authors = array();


	/**
	 * Defines the categories the item belongs to
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
	 * the contributors (name, email, uri)
	 *
	 * @var	array
	 */
	private $contributors = array();


	/**
	 * Generator
	 *
	 * @var	string
	 */
	private $generator;


	/**
	 * Defines a small image which provides iconic visual identification for the feed.
	 *
	 * @var	string
	 */
	private $icon;


	/**
	 * Defines the id for the item (=URI)
	 *
	 * @var	string
	 */
	private $id;


	/**
	 * Items
	 *
	 * @var	array
	 */
	private $items = array();


	/**
	 * Links
	 *
	 * @var	array
	 */
	private $links;


	/**
	 * Defines a larger image which provides visual identification for the feed.
	 *
	 * @var	string
	 */
	private $logo;


	/**
	 * Conveys information about rights, e.g. copyrights, held in and over the feed
	 *
	 * @var	string
	 */
	private $rights;


	/**
	 * Subtitle
	 *
	 * @var	string
	 */
	private $subtitle;


	/**
	 * Title
	 *
	 * @var	string
	 */
	private $title;


	/**
	 * Defines the updated date for the feed
	 *
	 * @var	int
	 */
	private $updatedDate;


	/**
	 * The default constructor
	 *
	 * @param	string $title			The title off the feed.
	 * @param	string $id				The id of the feed (URI).
	 * @param	array[optional] $items	An array with SpoonFeedRSSItems.
	 */
	public function __construct($title, $id, array $items = array())
	{
		// set properties
		$this->setTitle($title);
		$this->setId($id);

		// loop items and add them
		foreach($items as $item) $this->addItem($item);
	}


	/**
	 * Add an author for the feed.
	 *
	 * @param	array $author	The author values (name, email, uri).
	 */
	public function addAuthor($author)
	{
		$this->authors[] = $author;
	}


	/**
	 * Add a category for the feed.
	 *
	 * @param	array $category		The category with all the properties.
	 */
	public function addCategory($category)
	{
		$this->categories[] = $category;
	}


	/**
	 * Add a contributor for the feed.
	 *
	 * @param	string $name	The name of the contributor.
	 * @param	string $email	The email of the contributor.
	 * @param	string $uri		The uri of the contributor.
	 */
	public function addContributor($name, $email, $uri)
	{
		// create array
		$contributor['name'] = (string) $name;
		if($email != null) $contributor['email'] = (string) $email;
		if($uri != null) $contributor['uri'] = (string) $uri;

		// add property
		$this->contributors[] = $contributor;
	}


	/**
	 * Add an item to the feed.
	 *
	 * @param	SpoonFeedAtomRSSItem $item		A SpoonFeedAtomRSSItem that represents a single article in the feed.
	 */
	public function addItem(SpoonFeedAtomRSSItem $item)
	{
		$this->items[] = $item;
	}


	/**
	 * Add a link for the feed.
	 *
	 * @param	array $link		The link with all the properties.
	 */
	public function addLink($link)
	{
		$this->links[] = $link;
	}


	/**
	 * Builds the XML.
	 *
	 * @return	string		The fully build XML for the feed.
	 */
	public function buildXML()
	{
		// sort if needed
		//if($this->getSorting()) $this->sort();

		// init xml
		$XML = '<?xml version="1.0" encoding="' . strtolower($this->getCharset()) . '" ?>' . "\n";
		$XML .= '<feed xmlns="http://www.w3.org/2005/Atom">' . "\n";

		// insert title
		$XML .= ' <title>' . $this->getTitle() . '</title>' . "\n";

		// insert subtitle
		$subtitle = $this->getSubtitle();
		if($subtitle != null) $XML .= ' <subtitle>' . $subtitle . '</subtitle>' . "\n";

		// insert authors
		$authors = $this->getAuthors();
		if(!empty($authors))
		{
			foreach($authors as $author)
			{
				$XML .= '	<author>' . "\n";
				$XML .= '		<name>' . $author['name'] . '</name>' . "\n";
				if(isset($author['email'])) $XML .= '		<email>' . $author['email'] . '</email>' . "\n";
				if(isset($author['uri'])) $XML .= '		<uri>' . $author['uri'] . '</uri>' . "\n";
				$XML .= '	</author>' . "\n";
			}
		}

		// insert contributors
		$contributors = $this->getContributors();
		if(!empty($contributors))
		{
			foreach($contributors as $contributor)
			{
				$XML .= '	<contributor>' . "\n";
				$XML .= '		<name>' . $contributor['name'] . '</name>' . "\n";
				if(isset($author['email'])) $XML .= '		<email>' . $contributor['email'] . '</email>' . "\n";
				if(isset($author['uri'])) $XML .= '		<uri>' . $contributor['uri'] . '</uri>' . "\n";
				$XML .= '	</contributor>' . "\n";
			}
		}

		// insert id
		$XML .= '	<id>' . $this->getId() . '</id>' . "\n";

		// insert updated date
		$updated = $this->getUpdatedDate();
		if($updated != null) $XML .= '	<updated>' . date('Y-m-d\TH:i:s\Z', $updated) . '</updated>' . "\n";

		// insert links
		$links = $this->getLinks();
		if(!empty($links))
		{
			foreach($links as $link)
			{
				$XML .= '	<link ';

				// loop link attributes
				foreach($link as $key => $value)
				{
					$XML .= $key . '="' . ($key == 'href' ? htmlentities($value) : $value) . '" ';
				}

				$XML .= '/>' . "\n";
			}
		}

		// insert categories
		$categories = $this->getCategories();
		if(!empty($categories))
		{
			foreach($categories as $category)
			{
				$XML .= '	<category ';

				// loop category attributes
				foreach($category as $key => $value)
				{
					$XML .= $key . '="' . $value . '" ';
				}

				$XML .= '/>' . "\n";
			}
		}

		// insert rights
		$rights = $this->getRights();
		if($rights != null) $XML .= ' <rights>' . $rights . '</rights>' . "\n";

		// insert generator
		$generator = $this->getGenerator();
		if($generator != null) $XML .= ' <generator>' . $generator . '</generator>' . "\n";

		// insert icon
		$icon = $this->getIcon();
		if($icon != null) $XML .= ' <icon>' . $icon . '</icon>' . "\n";

		// insert logo
		$logo = $this->getLogo();
		if($logo != null) $XML .= ' <logo>' . $logo . '</logo>' . "\n";

		// insert items
		foreach($this->getItems() as $item)
		{
			$XML .= $item->parse();
		}

		// close feed
		$XML .= '</feed>';

		return $XML;
	}


	/**
	 * Get the authors.
	 *
	 * @return	array	An array with all authors.
	 */
	public function getAuthors()
	{
		return $this->authors;
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
	 * Get the charset.
	 *
	 * @return	string
	 */
	public function getCharset()
	{
		return $this->charset;
	}


	/**
	 * Get the contributors.
	 *
	 * @return	array	An array with all categories.
	 */
	public function getContributors()
	{
		return $this->contributors;
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
	 * Get the icon for the feed.
	 *
	 * @return	string
	 */
	public function getIcon()
	{
		return $this->icon;
	}


	/**
	 * Get the id (URI).
	 *
	 * @return	string
	 */
	public function getId()
	{
		return $this->id;
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
	 * Get the links.
	 *
	 * @return	array	An array with all links.
	 */
	public function getLinks()
	{
		return $this->links;
	}


	/**
	 * Get the logo for the feed.
	 *
	 * @return	string
	 */
	public function getLogo()
	{
		return $this->logo;
	}


	/**
	 * Get the rights for the feed.
	 *
	 * @return	string
	 */
	public function getRights()
	{
		return $this->rights;
	}


	/**
	 * Get the subtitle for the feed.
	 *
	 * @return	string
	 */
	public function getSubtitle()
	{
		return $this->subtitle;
	}


	/**
	 * Get the title for the feed.
	 *
	 * @return	string
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * Get the updated date as a UNIX timestamp.
	 *
	 * @return	int
	 */
	public function getUpdatedDate()
	{
		return $this->updatedDate;
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
		if(!isset($XML->title) || !isset($XML->id) || !isset($XML->entry)) return false;

		// loop items
		foreach($XML->entry as $item)
		{
			// validate items
			if(!SpoonFeedAtomRSSItem::isValid($item)) return false;
		}

		// fallback
		return true;
	}


	/**
	 * Reads an feed into a SpoonRSS object.
	 *
	 * @return	SpoonAtomRSS				Returns as an instance of SpoonAtomRSS.
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
		$title = (string) $XML->title;
		$id = (string) $XML->id;

		// create instance
		$RSS = new SpoonFeedAtomRSS($title, $id);

		// add authors
		if(isset($XML->author))
		{
			foreach($XML->author as $author)
			{
				// get the values
				$author['name'] = (string) $XML->author->name;
				$author['email'] = (isset($XML->author->email)) ? (string) $XML->author->email : null;
				$author['uri'] = (isset($XML->author->uri)) ? (string) $XML->author->uri : null;

				// set the values
				$RSS->addAuthor($author);
			}
		}

		// add contributors
		if(isset($XML->contributor))
		{
			foreach($XML->contributor as $contributor)
			{
				$name = (string) $contributor['name'];
				$email = isset($contributor['scheme']) ? (string) $contributor['email'] : null;
				$uri = isset($contributor['label']) ? (string) $contributor['uri$contributor'] : null;

				// set property
				$RSS->addContributor($name, $email, $uri);
			}
		}

		// add categories
		if(isset($XML->category))
		{
			foreach($XML->category as $category)
			{
				// build category
				$cat['term'] = (string) $category['term'];
				if(isset($category['scheme'])) $cat['scheme'] = (string) $category['scheme'];
				if(isset($category['label'])) $cat['label'] = (string) $category['label'];

				// set property
				$RSS->addCategory($cat);
			}
		}

		// add links
		if(isset($XML->link))
		{
			foreach($XML->link as $link)
			{
				// build link
				$aLink['href'] = $link['href'];
				if(isset($link['rel'])) $aLink['rel'] = $link['rel'];
				if(isset($link['type'])) $aLink['type'] = $link['type'];
				if(isset($link['title'])) $aLink['title'] = $link['title'];
				if(isset($link['hreflang'])) $aLink['hreflang'] = $link['hreflang'];
				if(isset($link['length'])) $aLink['length'] = $link['length'];

				// set property
				$RSS->addLink($aLink);
			}
		}

		// add items
		foreach($XML->entry as $item)
		{
			// try to read
			try
			{
				// read xml
				$item = SpoonFeedAtomRSSItem::readFromXML($item);
				$RSS->addItem($item);
			}

			// catch exceptions
			catch(Exception $e)
			{
				// ignore exceptions
			}
		}

		// set updated date
		if(isset($XML->updated)) $RSS->setUpdatedDate((int) strtotime($XML->updated));

		// set generator
		if(isset($XML->generator)) $RSS->setGenerator((string) $XML->generator);

		// set icon
		if(isset($XML->icon)) $RSS->setIcon((string) $XML->icon);

		// set logo
		if(isset($XML->logo)) $RSS->setLogo((string) $XML->logo);

		// set rights
		if(isset($XML->rights)) $RSS->setRights((string) $XML->rights);

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
	 * Set the generator for the feed.
	 *
	 * @param	string[optional] $generator		The generator of the feed, if not given "Spoon/<SpoonVersion>" will be used.
	 */
	public function setGenerator($generator = null)
	{
		$this->generator = ($generator == null) ? 'Spoon/' . SPOON_VERSION : (string) $generator;
	}


	/**
	 * Set the icon for the feed.
	 *
	 * @param	string $icon	The url for the icon .
	 */
	public function setIcon($icon)
	{
		$this->icon = (string) $icon;
	}


	/**
	 * Set the id (URI).
	 *
	 * @param	string $id	The id (URI) of the item (example: http://example.com/blog/1234).
	 */
	public function setId($id)
	{
		// redefine var
		$id = (string) $id;

		// validate
		if(!SpoonFilter::isURL($id)) throw new SpoonFeedException('This (' . $id . ') isn\'t a valid link.');

		// set property
		$this->id = $id;
	}


	/**
	 * Set the icon for the feed.
	 *
	 * @param	string $logo	The url for the logo .
	 */
	public function setLogo($logo)
	{
		$this->logo = (string) $logo;
	}


	/**
	 * Set the rights for the feed.
	 *
	 * @param	string $rights	Conveys information about rights, e.g. copyrights, held in and over the feed.
	 */
	public function setRights($rights)
	{
		$this->rights = (string) $rights;
	}


	/**
	 * Set the subtitle for the feed.
	 *
	 * @param	string $subtitle	The subtitle of the feed.
	 */
	public function setSubtitle($subtitle)
	{
		$this->subtitle = (string) $subtitle;
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
	 * Set the updated date.
	 *
	 * @param	int $updatedDate	The updated date as a UNIX-timestamp.
	 */
	public function setUpdatedDate($updatedDate)
	{
		$this->updatedDate = (int) $updatedDate;
	}
}