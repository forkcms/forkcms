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
 * This base class provides all the methods used by Atom RSS-items.
 *
 * @package		spoon
 * @subpackage	feed
 *
 *
 * @author		Lowie Benoot <lowiebenoot@netlash.com>
 * @since		1.1.0
 */
class SpoonFeedAtomRSSItem
{
	/**
	 * The authors (name, email, uri)
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
	 * Specifies the content of the item
	 *
	 * @var	string
	 */
	private $content;


	/**
	 * the contributors (name, email, uri)
	 *
	 * @var	array
	 */
	private $contributors = array();


	/**
	 * Defines the id for the item (=URI)
	 *
	 * @var	string
	 */
	private $id;


	/**
	 * Links
	 *
	 * @var	array
	 */
	private $links;


	/**
	 * Defines the last-publication date for the item
	 *
	 * @var	int
	 */
	private $publicationDate;


	/**
	 * Conveys information about rights, e.g. copyrights, held in and over the item
	 *
	 * @var	string
	 */
	private $rights;


	/**
	 * summary for the item
	 *
	 * @var	string
	 */
	private $summary;


	/**
	 * Defines the title of the item
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
	 * Default constructor.
	 *
	 * @param	string $title		The title for the item.
	 * @param	string $id			The id of the item (URI).
	 * @param	string $summary		The summary for the item.
	 */
	public function __construct($title, $id, $summary)
	{
		// set properties
		$this->setTitle($title);
		$this->setId($id);
		$this->setSummary($summary);
	}


	/**
	 * Add an author for the item.
	 *
	 * @param	array $author	The author values (name, email, uri).
	 */
	public function addAuthor($author)
	{
		$this->authors[] = $author;
	}


	/**
	 * Add a category for the item.
	 *
	 * @param	array $category		The category with all the properties.
	 */
	public function addCategory($category)
	{
		$this->categories[] = $category;
	}


	/**
	 * Add a contributor for the item.
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
	 * Add a link for the item.
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
	 * @return	string		The fully build XML for an item.
	 */
	public function buildXML()
	{
		// init xmlstring
		$XML = '<entry>' . "\n";

		// insert title
		$XML .= '	<title>' . $this->getTitle() . '</title>' . "\n";

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

		// insert publication date
		$published = $this->getPublicationDate();
		if($published != null) $XML .= '	<published>' . date('Y-m-d\TH:i:s\Z', $published) . '</published>' . "\n";

		// insert rights
		$rights = $this->getRights();
		if($rights != null) $XML .= ' <rights>' . $rights . '</rights>';

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

		// insert summary
		$XML .= '	<summary type="html">' . "\n";
		$XML .= '		<![CDATA[' . "\n";
		$XML .= '			' . $this->getSummary() . "\n";
		$XML .= '		]]>' . "\n";
		$XML .= '	</summary>' . "\n";

		// insert content
		$content = $this->getContent();
		if($content != null)
		{
			$XML .= '	<content type="html">' . "\n";
			$XML .= '		<![CDATA[' . "\n";
			$XML .= '			' . $content . "\n";
			$XML .= '		]]>' . "\n";
			$XML .= '	</content>' . "\n";
		}

		// close item
		$XML .= '</entry>' . "\n";

		// return
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
	 * Get the content.
	 *
	 * @return	string
	 */
	public function getContent()
	{
		return $this->content;
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
	 * Get the id (URI).
	 *
	 * @return	string
	 */
	public function getId()
	{
		return $this->id;
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
	 * Get the publication date.
	 *
	 * @return	int
	 */
	public function getPublicationDate()
	{
		return $this->publicationDate;
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
	 * Get the summary.
	 *
	 * @return	string
	 */
	public function getSummary()
	{
		return $this->summary;
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
	 * Get the updated date as a UNIX timestamp.
	 *
	 * @return	int
	 */
	public function getUpdatedDate()
	{
		return $this->updatedDate;
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
		if(!isset($item->title) || !isset($item->id) || !isset($item->summary)) return false;

		// fallback
		return true;
	}


	/**
	 * Parse the item
	 *
	 * @return	string	The XML for the item.
	 */
	public function parse()
	{
		return $this->buildXML();
	}


	/**
	 * Read an item from a SimpleXMLElement.
	 *
	 * @return	SpoonAtomRSSItem				An instance of SpoonAtomRSSItem
	 * @param	SimpleXMLElement $item			The XML-element that represents a single item in the feed.
	 */
	public static function readFromXML(SimpleXMLElement $item)
	{
		// get title, id and summary
		$title = (string) $item->title;
		$id = (string) $item->id;
		$summary = (string) $item->summary;

		// create instance
		$rssItem = new SpoonFeedAtomRSSItem($title, $id, $summary);

		// set updated date
		if(isset($item->updated)) $rssItem->setUpdatedDate((int) strtotime($item->updated));

		// add authors
		if(isset($item->author))
		{
			foreach($item->author as $author)
			{
				// get the values
				$author['name'] = (string) $item->author->name;
				if(isset($item->author->email)) $author['email'] = (string) $item->author->email;
				if(isset($item->author->uri)) $author['uri'] = (string) $item->author->uri;

				// set the values
				$rssItem->addAuthor($author);
			}
		}

		// set content
		if(isset($item->content)) $rssItem->setContent((string) $item->content);

		// add links
		if(isset($item->link))
		{
			foreach($item->link as $link)
			{
				// build link
				$aLink['href'] = $link['href'];
				if(isset($link['rel'])) $aLink['rel'] = $link['rel'];
				if(isset($link['type'])) $aLink['type'] = $link['type'];
				if(isset($link['title'])) $aLink['title'] = $link['title'];
				if(isset($link['hreflang'])) $aLink['hreflang'] = $link['hreflang'];
				if(isset($link['length'])) $aLink['length'] = $link['length'];

				// set property
				$rssItem->addLink($aLink);
			}
		}

		// add categories
		if(isset($item->category))
		{
			foreach($item->category as $category)
			{
				// build category
				$cat['term'] = (string) $category['term'];
				if(isset($category['scheme'])) $cat['scheme'] = (string) $category['scheme'];
				if(isset($category['label'])) $cat['label'] = (string) $category['label'];

				// set property
				$rssItem->addCategory($cat);
			}
		}

		// add contributors
		if(isset($item->contributor))
		{
			foreach($item->contributor as $contributor)
			{
				$name = (string) $contributor['name'];
				$email = isset($contributor['scheme']) ? (string) $contributor['email'] : null;
				$uri = isset($contributor['label']) ? (string) $contributor['uri$contributor'] : null;

				// set property
				$rssItem->addContributor($name, $email, $uri);
			}
		}

		// set publication date
		if(isset($item->published)) $rssItem->setPublicationDate((int) strtotime($item->published));

		// set rights
		if(isset($XML->rights)) $rssItem->setRights((string) $XML->rights);

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
	 * Set the content.
	 *
	 * @param	string $content		The content of the item.
	 */
	public function setContent($content)
	{
		$this->content = (string) $content;
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
	 * Set the publication date.
	 *
	 * @param	int $publicationDate	The publication date as a UNIX-timestamp.
	 */
	public function setPublicationDate($publicationDate)
	{
		$this->publicationDate = (int) $publicationDate;
	}


	/**
	 * Set the rights for the item.
	 *
	 * @param	string $rights	Conveys information about rights, e.g. copyrights, held in and over the item.
	 */
	public function setRights($rights)
	{
		$this->rights = (string) $rights;
	}


	/**
	 * Set the summary.
	 *
	 * @param	string $summary		The content of the item.
	 */
	public function setSummary($summary)
	{
		$this->summary = (string) $summary;
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