<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			xml
 * @subpackage		atom
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.4
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** Spoon RSS execption class */
require_once 'spoon/xml/atom/exception.php';

/** Spoon Filter class */
require_once 'spoon/filter/filter.php';


/**
 * This base class provides all the methods used by Atom-files
 *
 * @package			xml
 * @subpackage		atom
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.4
 */
class SpoonAtomEntry
{
	/**
	 * An array of the authors of the entry.
	 *
	 * @var	array
	 */
	private $aAuthors;


	/**
	 * An array of the categories the entry belongs to
	 *
	 * @var	array
	 */
	private $aCategories;


	/**
	 * An array of the contributors of the entry
	 *
	 * @var	array
	 */
	private $aContributors;


	/**
	 * An array of related links
	 *
	 * @var	array
	 */
	private $aLinks;


	/**
	 * Contains or links to the complete content of the entry.
	 * Content must be provided if there is no alternate link, and should be provided if there is no summary.
	 *
	 * @var	array
	 */
	private $content;


	/**
	 * Identifies the entry using a universally unique and permanent URI.
	 *
	 * @var	string
	 */
	private $id;


	/**
	 * Contains the time of the initial creation or first availability of the entry.
	 *
	 * @var	int
	 */
	private $published;


	/**
	 * The rights for the entry
	 *
	 * @var	array
	 */
	private $rights;


	/**
	 * The source of the entry
	 *
	 * @var	array
	 */
	private $source;


	/**
	 * Conveys a short summary, abstract, or excerpt of the entry.
	 * Summary should be provided if there either is no content provided for the entry, or that content is not inline (i.e., contains a src attribute), or if the content is encoded in base64.
	 *
	 * @var	array
	 */
	private $summary;


	/**
	 * Contains a human readable title for the entry. This value should not be blank.
	 *
	 * @var	string
	 */
	private $title;


	/**
	 * Indicates the last time the entry was modified in a significant way. This value need not change after a typo is fixed, only after a substantial modification. Generally, different entries in a feed will have different updated timestamps.
	 *
	 * @var	int
	 */
	private $updated;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string $id
	 * @param	string $title
	 * @param	int $updated
	 */
	public function __construct($id, $title, $titleType = 'text', $updated)
	{
		$this->setId($id);
		$this->setTitle($title, $titleType);
		$this->setUpdated($updated);
	}


	/**
	 * Names one author of the entry. An entry may have multiple authors.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $uri
	 * @param	string[optiona] $email
	 */
	public function addAuthor($name, $uri = null, $email = null)
	{
		// validate params
		if($email !== null && !SpoonFilter::isEmail($email)) throw new SpoonAtomException('Invalid email ('. $email .')');

		// build array
		$aAuthor['name'] = (string) $name;
		if($uri !== null) $aAuthor['uri'] = (string) $uri;
		if($email !== null) $aAuthor['email'] = (string) $email;

		// add
		$this->aAuthors[] = $aAuthor;
	}


	/**
	 * Adds a category that the entry belongs to.
	 *
	 * @return	void
	 * @param 	string $term
	 * @param	string[optional] $scheme
	 * @param	string[optional] $label
	 */
	public function addCategory($term, $scheme = null, $label = null)
	{
		// build array
		$aCategory['term'] = (string) $term;
		if($scheme !== null) $aCategory['scheme'] = (string) $scheme;
		if($label !== null) $aCategory['label'] = (string) $label;

		// add category
		$this->aCategories[] = $aCategory;
	}


	/**
	 * Names one contributor of the entry. An entry may have multiple authors.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $uri
	 * @param	string[optiona] $email
	 */
	public function addContributor($name, $uri = null, $email = null)
	{
		// validate params
		if($email !== null && !SpoonFilter::isEmail($email)) throw new SpoonAtomException('Invalid email ('. $email .')');

		// build array
		$aContributor['name'] = (string) $name;
		if($uri !== null) $aContributor['uri'] = (string) $uri;
		if($email !== null) $aContributor['email'] = (string) $email;

		// add
		$this->aContributors[] = $aContributor;
	}


	/**
	 * Adds a related Web page. The type of relation is defined by the rel attribute.
	 *
	 * An entry is limited to one alternate per type and hreflang.
	 * An entry must contain an alternate link if there is no content element.
	 *
	 * @return	void
	 * @param	string $href
	 * @param	string[optional] $rel
	 * @param	string[optional] $type
	 * @param	string[optional] $title
	 * @param	int[optional] $length
	 * @param	string[optional] $hreflang
	 */
	public function addLink($href, $rel = 'alternate', $type = null, $title = null, $length = null, $hreflang = null)
	{
		// build array
		$aLink['href'] = str_replace(array('&amp;', '&'), array('&', '&amp;'), (string) $href);
		$aLink['rel'] = (string) $rel;
		if($type !== null) $aLink['type'] = (string) $type;
		if($title !== null) $aLink['title'] = (string) $title;
		if($length !== null) $aLink['length'] = (int) $length;
		if($hreflang !== null) $aLink['hreflang'] = (string) $hreflang;

		// add
		$this->aLinks[] = $aLink;
	}


	/**
	 * Build the XML
	 *
	 * @return	string
	 */
	public function buildXML()
	{
		// init xmlstring
		$xml = '<entry>'."\n";

		// insert id
		$xml .= '	<id><![CDATA['. $this->getId() .']]></id>'."\n";

		// insert title
		$aTitle = (array) $this->getTitle();
		$xml .= '	<title type="'. $aTitle['type'] .'"><![CDATA['. $aTitle['value'] .']]></title>'."\n";

		// insert updated
		$xml .= '	<updated>'. date('c', $this->getUpdated()) .'</updated>'."\n";

		// authors
		foreach($this->getAuthors() as $author)
		{
			$xml .= '	<author>' ."\n";
			$xml .= '		<name><![CDATA['. $author['name'] .']]></name>'."\n";
			if(isset($author['uri'])) $xml .= '		<uri>'. $author['uri'] .'</uri>'."\n";
			if(isset($author['email'])) $xml .= '		<email>'. $author['email'] .'</email>'."\n";
			$xml .= '	</author>' ."\n";
		}

		// insert content
		$aContent = $this->getContent();
		if(!empty($aContent))
		{
			$xml .= '	<content';
			if(isset($aContent['type'])) $xml .=' type="'. $aContent['type'] .'"';
			if(isset($aContent['src'])) $xml .= ' src="'. $aContent['src'] .'" ';
			$xml .= '>'."\n";
			$xml .= '	<![CDATA['."\n";
			$xml .= '			'. $aContent['value'] ."\n";
			$xml .= '	]]>'."\n";
			$xml .= '	</content>'."\n";
		}

		// insert links
		foreach($this->getLinks() as $link)
		{
			$xml .= '	<link rel="'.$link['rel'] .'" href="'. $link['href'] .'"';

			if(isset($link['type'])) $xml .=' type="'. $link['type'] .'"';
			if(isset($link['hreflang'])) $xml .=' hreflang="'. $link['hreflang'] .'"';
			if(isset($link['title'])) $xml .=' title="'. $link['title'] .'"';
			if(isset($link['length'])) $xml .=' length="'. $link['length'] .'"';

			$xml .= ' />' ."\n";
		}

		// insert summary
		$aSummary = $this->getSummary();
		if(!empty($aSummary))
		{
			$xml .= '	<summary type="'. $aSummary['type'] .'"><![CDATA['."\n";
			$xml .= '			'. $aSummary['text'] ."\n";
			$xml .= '	]]></summary>'."\n";
		}

		// insert categories
		foreach($this->getCategories() as $category)
		{
			$xml .= '	<category term="'.$category['term'] .'"';

			if(isset($category['scheme'])) $xml .=' scheme="'. $category['scheme'] .'"';
			if(isset($category['label'])) $xml .=' label="'. $category['label'] .'"';

			$xml .= ' />' ."\n";
		}

		// insert contrubutors
		foreach($this->getContributors() as $contributor)
		{
			$xml .= '	<contributor>' ."\n";
			$xml .= '		<name><![CDATA['. $contributor['name'] .']]></name>'."\n";
			if(isset($contributor['uri'])) $xml .= '		<uri>'. $contributor['uri'] .'</uri>'."\n";
			if(isset($contributor['email'])) $xml .= '		<email>'. $contributor['email'] .'</email>'."\n";
			$xml .= '	</contributor>' ."\n";
		}

		// insert published
		$published = $this->getPublished();
		if($published !== 0) $xml .= '	<published>'. date('c', $published) .'</published>'."\n";

		// insert source
		$aSource = $this->getSource();
		if(!empty($aSource))
		{
			$xml .= '	<source>'."\n";
			$xml .= '		<id><![CDATA['. $aSource['id'] .'</id>'."\n";
			$xml .= '		<updated>'. date('c', $aSource['updated']) .'</updated>'."\n";
			if(isset($aSource['title'])) $xml .= '		<title><![CDATA['. $aSource['title'] .']]></title>'."\n";
			$xml .= '	</source>'."\n";
		}

		// insert rights
		$aRights = $this->getRights();
		if(!empty($aRights))
		{
			$xml .= '	<rights type="'. $aRights['type'] .'"><![CDATA['."\n";
			$xml .= '			'. $aRights['text'] ."\n";
			$xml .= '	]]></rights>'."\n";
		}

		$xml .= '</entry>'."\n";


		// return
		return $xml;
	}


	/**
	 * Get the authors of the entry.
	 *
	 * @return	array
	 */
	public function getAuthors()
	{
		return (array) $this->aAuthors;
	}


	/**
	 * Get th categories of the entry
	 *
	 * @return	array
	 */
	public function getCategories()
	{
		return (array) $this->aCategories;
	}


	/**
	 * Get the content of the entry
	 *
	 * @return	array
	 */
	public function getContent()
	{
		return (array) $this->content;
	}


	/**
	 * Get the contributors
	 *
	 * @return	array
	 */
	public function getContributors()
	{
		return (array) $this->aContributors;
	}


	/**
	 * Get the identifier
	 *
	 * @return	string
	 */
	public function getId()
	{
		return (string) $this->id;
	}


	/**
	 * Get the links
	 *
	 * @return	array
	 */
	public function getLinks()
	{
		return (array) $this->aLinks;
	}


	/**
	 * Get the time of the initial creation or first availability of the entry
	 *
	 * @return	int
	 */
	public function getPublished()
	{
		return (int) $this->published;
	}


	/**
	 * Get the rights if the entry
	 *
	 * @return	array
	 */
	public function getRights()
	{
		return (array) $this->rights;
	}


	/**
	 * Get the source of the entry
	 *
	 * @return	array
	 */
	public function getSource()
	{
		return (array) $this->source;
	}


	/**
	 * Get the summary of the entry
	 *
	 * @return	array
	 */
	public function getSummary()
	{
		return (array) $this->summary;
	}


	/**
	 * Get the human readable title
	 *
	 * @return	string
	 */
	public function getTitle()
	{
		return (array) $this->title;
	}


	/**
	 * Get the last time the entry was modified in a significant way.
	 *
	 * @return	int
	 */
	public function getUpdated()
	{
		return (int) $this->updated;
	}


	/**
	 * Validate if the given XML is valid
	 *
	 * @return	bool
	 * @param	SimpleXMLElement $entry
	 */
	public static function isValid($entry)
	{
		// validate
		if(get_class($entry) !== 'SimpleXMLElement') throw new SpoonAtomException('This isn\'t a valid object. Only SimpleXMLElement is allowed.');

		// are all needed elements present?
		if(!isset($entry->id) || !isset($entry->title) || !isset($entry->updated)) return false;

		// fallback
		return true;
	}


	/**
	 * Get the parsed XML
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
	 * @return	SpoonAtomEntry
	 * @param	SimpleXMLElement $item
	 */
	public static function readFromXML($item)
	{
		// validate
		if(get_class($item) != 'SimpleXMLElement') throw new SpoonAtomException('This isn\'t a valid object. Only SimpleXMLElement is allowed.');

		// get variables
		$id = utf8_decode((string) $item->id);
		$title = utf8_decode((string) $item->title);
		$titleType = (isset($item->title['type']) && $item->title['type'] != '') ? utf8_decode((string) $item->title['type']) : 'text';
		$updated = strtotime($item->updated);

		// create basic entry
		$atomEntry = new SpoonAtomEntry($id, $title, $titleType, $updated);

		// authors present?
		if(isset($item->author))
		{
			// loop authors
			foreach($item->author as $author)
			{
				// get variables
				$name = utf8_decode((string) $author->name);
				$uri = (isset($author->uri)) ? utf8_decode((string) $author->uri) : null;
				$email = (isset($author->email)) ? utf8_decode((string) $author->email) : null;

				// add author
				$atomEntry->addAuthor($name, $uri, $email);
			}
		}

		// categories present?
		if(isset($item->category))
		{
			// loop categories
			foreach($item->category as $category)
			{
				// get variables
				$term = utf8_decode((string) $category['term']);
				$scheme = (isset($category['scheme'])) ? utf8_decode((string) $category['scheme']) : null;
				$label = (isset($category['label'])) ? utf8_decode((string) $category['label']) : null;

				// add category
				$atomEntry->addCategory($term, $scheme, $label);
			}
		}

		// contributors present
		if(isset($item->contributor))
		{
			// loop contributors
			foreach($item->contributor as $contributor)
			{
				// get variables
				$name = utf8_decode((string) $author->name);
				$uri = (isset($author->uri)) ? utf8_decode((string) $author->uri) : null;
				$email = (isset($author->email)) ? utf8_decode((string) $author->email) : null;

				// add contributor
				$atomEntry->addContributor($name, $uri, $email);
			}
		}

		// links present?
		if(isset($item->link))
		{
			// loop links
			foreach($item->link as $link)
			{
				// get variables
				$href = (string) utf8_decode($link['href']);
				$rel = (isset($link['rel'])) ? utf8_decode((string) $link['rel']) : null;
				$type = (isset($link['type'])) ? utf8_decode((string) $link['type']) : null;
				$title = (isset($link['title'])) ? utf8_decode((string) $link['title']) : null;
				$length = (isset($link['length'])) ? utf8_decode((int) $link['length']) : null;
				$hreflang = (isset($link['hreflang'])) ? utf8_decode((string) $link['hreflang']) : null;

				// add link
				$atomEntry->addLink($href, $rel, $type, $title, $length, $hreflang);
			}
		}

		// content available
		if(isset($item->content))
		{
			// get variables
			$text = utf8_decode((string) $item->content);
			$type = (isset($item->content['type'])) ? utf8_decode((string) $item->content['type']) : 'text';
			$src = (isset($item->content['src'])) ? utf8_decode((string) $item->content['src']) : null;

			// set content
			$atomEntry->setContent($text, $type, $src);
		}

		// set published
		if(isset($item->published)) $atomEntry->setPublished(strtotime((string) $item->published));

		// rights available
		if(isset($item->rights))
		{
			// get variables
			$text = utf8_decode((string) $item->rights);
			$type = (isset($item->rights['type'])) ? utf8_decode((string) $item->rights['type']) : 'text';
			$src = (isset($item->rights['src'])) ? utf8_decode((string) $item->rights['src']) : null;

			// set rights
			$atomEntry->setRights($text, $type, $src);
		}

		// source available
		if(isset($item->source))
		{
			// get variables
			$id = utf8_decode((string) $item->source->id);
			$title = utf8_decode((string) $item->source->title);
			$updated = (int) strtotime((string) $item->source->updated);

			// set source
			$atomEntry->setSource($id, $title, $updated);
		}

		// summary available
		if(isset($item->summary))
		{
			// get variables
			$text = utf8_decode((string) $item->summary);
			$type = (isset($item->summary['type'])) ? utf8_decode((string) $item->summary['type']) : 'text';
			$src = (isset($item->summary['src'])) ? utf8_decode((string) $item->summary['src']) : null;

			// set summary
			$atomEntry->setSummary($text, $type, $src);
		}

		// return
		return $atomEntry;
	}


	/**
	 * Contains or links to the complete content of the entry.
	 *
	 * In the most common case, the type attribute is either text, html, xhtml.
	 * Otherwise, if the src attribute is present, it represents the URI of where the content can be found. The type attribute, if present, is the media type of the content.
	 * Otherwise, if the type attribute ends in +xml or /xml, then an xml document of this type is contained inline.
	 * Otherwise, if the type attribute starts with text, then an escaped document of this type is contained inline.
	 * Otherwise, a base64 encoded document of the indicated media type is contained inline.
	 *
	 * @return	void
	 * @param	string $text
	 * @param	string[optional] $type
	 * @param	string[optional] $src
	 */
	public function setContent($text, $type = 'text', $src = null)
	{
		// create array
		$aContent['value'] = (string) $text;
		if($type !== null) $aContent['type'] = (string) $type;
		if($src !== null) $aContent['src'] = (string) $src;

		// set property
		$this->content = $aContent;
	}


	/**
	 * Identifies the entry using a universally unique and permanent URI
	 *
	 * @return	void
	 * @param	string $id
	 */
	public function setId($id)
	{
		$this->id = (string) $id;
	}


	/**
	 * Set the the time of the initial creation or first availability of teh entry
	 *
	 * @return	void
	 * @param	int $timestamp
	 */
	public function setPublished($timestamp)
	{
		$this->published = (int) $timestamp;
	}


	/**
	 * Set the rights for the entry
	 *
	 * In the most common case, the type attribute is either text, html, xhtml.
	 * Otherwise, if the src attribute is present, it represents the URI of where the content can be found. The type attribute, if present, is the media type of the content.
	 * Otherwise, if the type attribute ends in +xml or /xml, then an xml document of this type is contained inline.
	 * Otherwise, if the type attribute starts with text, then an escaped document of this type is contained inline.
	 * Otherwise, a base64 encoded document of the indicated media type is contained inline.
	 *
	 * @return	void
	 * @param	string $text
	 * @param	string[optional] $type
	 * @param	string[optional] $src
	 */
	public function setRights($text, $type = 'text', $src = null)
	{
		// create array
		$aRights['value'] = (string) $text;
		if($type !== null) $aRights['type'] = (string) $type;
		if($src !== null) $aRights['src'] = (string) $src;

		// set property
		$this->aRights = $aRights;
	}


	/**
	 * Set the source
	 *
	 * @return	void
	 * @param	string $id
	 * @param	string $title
	 * @param	int $updated
	 */
	public function setSource($id, $title, $updated)
	{
		// create array
		$aSource['id'] = (string) $id;
		$aSource['title'] = (string) $title;
		$aSource['updated'] = (int) $updated;

		// set property
		$this->source = $aSource;
	}


	/**
	 * Contains or links to the complete content of the entry.
	 *
	 * In the most common case, the type attribute is either text, html, xhtml.
	 * Otherwise, if the src attribute is present, it represents the URI of where the content can be found. The type attribute, if present, is the media type of the content.
	 * Otherwise, if the type attribute ends in +xml or /xml, then an xml document of this type is contained inline.
	 * Otherwise, if the type attribute starts with text, then an escaped document of this type is contained inline.
	 * Otherwise, a base64 encoded document of the indicated media type is contained inline.
	 *
	 * @return	void
	 * @param	string $text
	 * @param	string[optional] $type
	 * @param	string[optional] $src
	 */
	public function setSummary($text, $type = 'text', $src = null)
	{
		// create array
		$aSummary['value'] = (string) $text;
		if($type !== null) $aSummary['type'] = (string) $type;
		if($src !== null) $aSummary['src'] = (string) $src;

		// set property
		$this->summary = $aSummary;
	}


	/**
	 * Set a human readable title for the entry.
	 * Possible values for type are: text, html, xhtml
	 *
	 * @return	void
	 * @param	string $title
	 * @param	string[optional] $type
	 */
	public function setTitle($title, $type = 'text')
	{
		// possible types
		$aPossibleTypes = array('text', 'html', 'xhtml');

		// redefine
		$title = (string) $title;
		$type = (string) $type;

		// validate parameters
		if(!in_array($type, $aPossibleTypes)) throw new SpoonAtomException('Invalid type for title ('. $type .'). Possible values are: '. implode(', ', $aPossibleTypes) .'.');

		$this->title['type'] = $type;
		$this->title['value'] = $title;
	}


	/**
	 * Set the last modify-date
	 *
	 * @return	void
	 * @param	int $timestamp
	 */
	public function setUpdated($timestamp)
	{
		$this->updated = (int) $timestamp;
	}

}

?>