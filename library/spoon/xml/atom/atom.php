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

/** Spoon Atom execption class */
require_once 'spoon/xml/atom/exception.php';

/** Spoon Atom Entry class */
require_once 'spoon/xml/atom/atom_entry.php';

/** Spoon File class */
require_once 'spoon/filesystem/file.php';

/** Spoon HTTP class */
require_once 'spoon/http/http.php';


/**
 * This base class provides all the methods used by Atom-files
 *
 * @package			xml
 * @subpackage		atom
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			1.0.4
 */
class SpoonAtom
{
	/**
	 * Xml header
	 *
	 * @var	string
	 */
	const ATOM_HEADER = "Content-Type: application/xml; charset=";


	/**
	 * An array that contains the authors of the feed
	 *
	 * @var	array
	 */
	private $aAuthors = array();


	/**
	 * An array that contains the categories of the feed
	 *
	 * @var	array
	 */
	private $aCategories = array();


	/**
	 * An array that contains the contrubutors of the feed
	 *
	 * @var	array
	 */
	private $aContributors = array();


	/**
	 * The generator for the feed
	 *
	 * @var	array
	 */
	private $aGenerator = array();


	/**
	 * An array that contains links to related pages of the feed
	 *
	 * @var	array
	 */
	private $aLinks = array();


	/**
	 * The rights for the feed
	 *
	 * @var	array
	 */
	private $aRights = array();


	/**
	 * The subtitle for the feed
	 *
	 * @var	array
	 */
	private $aSubtitle = array();


	/**
	 * The charset
	 *
	 * @var	string
	 */
	private $charset = 'ISO-8859-15';


	/**
	 * The icon for the feed
	 *
	 * @var	string
	 */
	private $icon;


	/**
	 * The unique URI for the feed
	 *
	 * @var	string
	 */
	private $id;


	/**
	 * The logo for the feed
	 *
	 * @var	string
	 */
	private $logo;


	/**
	 * Must the items be sort on publication date?
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
	 * The title for the feed
	 *
	 * @var	string
	 */
	private $title;


	/**
	 * The last time the feed was build (UNIX timestamp)
	 *
	 * @var	int
	 */
	private $updated;


	/**
	 * Entries
	 *
	 * @var	array
	 */
	private $entries = array();


	/**
	 * Default constructor
	 *
	 * @param	string $id
	 * @param	string $title
	 * @param	int[optional] $updated
	 * @param	array[optional] $entries
	 */
	public function __construct($id, $title, $titleType = 'text', $updated = null, $entries = array())
	{
		// set properties
		$this->setId($id);
		$this->setTitle($title, $titleType);
		$this->setUpdated($updated);

		// loop items and add them
		foreach ((array) $entries as $entry) $this->addEntry($entry);
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

		// add
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
	 * Add an item to the feed
	 *
	 * @return	void
	 * @param	SpoonAtomEntry $entry
	 */
	public function addEntry($entry)
	{
		// allowed classes, will be extended
		$aAllowedClasses = array('SpoonAtomEntry');

		// validate
		if(!in_array(get_class($entry), $aAllowedClasses)) throw new SpoonAtomException('The specified item (type: '.get_class($entry).') isn\'t  valid.');

		// set property
		$this->entries[] = $entry;
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
		$xml .= '<feed xmlns="http://www.w3.org/2005/Atom">'."\n";

		// id
		$xml .= '	<id><![CDATA['. $this->getId() .']]></id>'."\n";

		// insert title
		$aTitle = (array) $this->getTitle();
		$xml .= '	<title type="'. $aTitle['type'] .'"><![CDATA['. $aTitle['value'] .']]></title>'."\n";

		// last build date
		$xml .= '	<updated>'. $this->getUpdated('c') .'</updated>'."\n";

		// authors
		foreach($this->getAuthors() as $author)
		{
			$xml .= '	<author>' ."\n";
			$xml .= '		<name><![CDATA['. $author['name'] .']]></name>'."\n";
			if(isset($author['uri'])) $xml .= '		<uri>'. $author['uri'] .'</uri>'."\n";
			if(isset($author['email'])) $xml .= '		<email>'. $author['email'] .'</email>'."\n";
			$xml .= '	</author>' ."\n";
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

		// generator
		$aGenerator = $this->getGenerator();
		if(!empty($aGenerator))
		{
			$xml .= '	<generator';
			if(isset($aGenerator['uri']) && $aGenerator['uri'] != '') $xml .= ' uri="'. $aGenerator['uri'] .'"';
			if(isset($aGenerator['version']) && $aGenerator['version'] != '') $xml .= ' version="'. $aGenerator['version'] .'"';
			$xml .= '><![CDATA['. $aGenerator['name'] .']]></generator>'."\n";
		}

		// icon
		if($this->getIcon() != '') $xml .= '	<icon>'. $this->getIcon() .'</icon>'."\n";

		// logo
		if($this->getLogo() != '') $xml .= '	<logo>'. $this->getLogo() .'</logo>'."\n";

		// insert rights
		$aRights = $this->getRights();
		if(!empty($aRights))
		{
			$xml .= '	<rights type="'. $aRights['type'] .'"><![CDATA['. $aRights['value'] .']]></rights>'."\n";
		}

		// insert subtitle
		$aSubtitle = $this->getSubtitle();
		if(!empty($aSubtitle))
		{
			$xml .= '	<subtitle type="'. $aSubtitle['type'] .'"><![CDATA['. $aSubtitle['value'] .']]></subtitle>'."\n";
		}

		// entries
		foreach($this->entries as $entry) $xml .= $entry->parse();

		// end xml
		$xml .= '</feed>';

		// return
		return $xml;
	}


	/**
	 * Compare Object for sorting on publication date
	 *
	 * @return	int
	 * @param	SpoonAtomEntry $object1
	 * @param	SpoonAtomEntry $object2
	 * @param	string[optional] $sortingMethod
	 */
	private static function compareObjects($object1, $object2)
	{
		// validate
		if(get_class($object1) != 'SpoonAtomEntry') throw new SpoonAtomException('This isn\'t a valid object.');
		if(get_class($object2) != 'SpoonAtomEntry') throw new SpoonAtomException('This isn\'t a valid object.');

		// if the object have the same updated date there are equal
		if($object1->getUpdated() == $object2->getUpdated()) return 0;

		if(SpoonAtom::$sortingMethod == 'asc')
		{
			// if the updated date is greater then the other return 1, so we known howto sort
			if($object1->getUpdated() > $object2->getUpdated()) return 1;

			// if the updated date is smaller then the other return -1, so we known howto sort
			if($object1->getUpdated() < $object2->getUpdated()) return -1;
		}
		else
		{
			// if the updated date is greater then the other return -1, so we known howto sort
			if($object1->getUpdated() > $object2->getUpdated()) return -1;

			// if the updated date is smaller then the other return 1, so we known howto sort
			if($object1->getUpdated() < $object2->getUpdated()) return 1;
		}
	}


	/**
	 * Get the authors
	 *
	 * @return	array
	 */
	public function getAuthors()
	{
		return (array) $this->aAuthors;
	}


	/**
	 * Get the added categories the feed belongs to.
	 *
	 * @return	array
	 */
	public function getCategories()
	{
		return (array) $this->aCategories;
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
	 * Get the added contributors for the feed
	 *
	 * @return	array
	 */
	public function getContributors()
	{
		return (array) $this->aContributors;
	}


	/**
	 * Get the added entries
	 *
	 * @return	array
	 */
	public function getEntries()
	{
		return (array) $this->entries;
	}


	/**
	 * Get the feed-generator
	 *
	 * @return	array
	 */
	public function getGenerator()
	{
		// if no generator was set, we should promote Spoon
		if(empty($this->aGenerator)) return array('name' => 'Spoon Library', 'uri' => 'http://www.spoon-library.be', 'version' => SPOON_VERSION);

		// return
		return (array) $this->aGenerator;
	}


	/**
	 * Get the url for the icon of the feed
	 *
	 * @return	string
	 */
	public function getIcon()
	{
		return (string) $this->icon;
	}


	/**
	 * Get the universally unique and permanent URI for the feed
	 *
	 * @return	string
	 */
	public function getId()
	{
		return (string) $this->id;
	}


	/**
	 * Get the added related pages for the feed
	 *
	 * @return	array
	 */
	public function getLinks()
	{
		return (array) $this->aLinks;
	}


	/**
	 * Get the url for the logo of the feed
	 *
	 * @return	string
	 */
	public function getLogo()
	{
		return (string) $this->logo;
	}


	/**
	 * Get the raw XML
	 *
	 * @return	string
	 */
	public function getRawXML()
	{
		return (string) $this->buildXML();
	}


	/**
	 * Get the rights-information for the feed
	 *
	 * @return	array
	 */
	public function getRights()
	{
		return (array) $this->aRights;
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
	 * Get the subtitle for the feed
	 *
	 * @return	array
	 */
	public function getSubtitle()
	{
		return (array) $this->aSubtitle;
	}


	/**
	 * Get the human readable title for the feed
	 *
	 * @return	array
	 */
	public function getTitle()
	{
		return (array) $this->title;
	}


	/**
	 * Get the time the feed was modified in a significant way
	 *
	 * @return	mixed
	 * @param	string[optional] $format
	 */
	public function getUpdated($format = null)
	{
		// set time if needed
		if($this->updated == 0) $this->updated = time();

		// format if needed
		if($format !== null) return date((string) $format, $this->updated);

		// return
		return (int) $this->updated;
	}


	/**
	 * Checks if the feed is valid
	 *
	 * @return	bool
	 * @param	string $url
	 * @param	string[option] $type
	 */
	public static function isValid($url, $type = 'url')
	{
		$aAllowedTypes = array('url', 'string');

		// redefine var
		$url = (string) $url;
		$type = (string) SpoonFilter::getValue($type, $aAllowedTypes, 'url');

		// validate
		if(!in_array($type, $aAllowedTypes)) throw new SpoonAtomException('This ('. $type .') isn\'t allowed. Only '. join(', ', $aAllowedTypes) .' are allowed.');
		if($type == 'url' && !SpoonFilter::isURL($url)) throw new SpoonAtomException('This ('. $url .') isn\'t a valid url.');

		// load xmlstring
		if($type == 'url')
		{
			// check if allow_url_fopen is enabled
			if(ini_get('allow_url_fopen') == 0) throw new SpoonAtomException('allow_url_fopen should be enabled, if you want to get a remote url.');

			// open the url
			$handle = @fopen($url, 'r');

			// validate the handle
			if($handle === false) throw new SpoonAtomException('Something went wrong while retrieving the url.');

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
		if(!isset($xml->id) || !isset($xml->title) || !isset($xml->updated) || !isset($xml->entry)) return false;

		// loop items
		foreach ($xml->entry as $entry)
		{
			// validate items
			if(!SpoonAtomEntry::isValid($entry)) return false;
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
		if($headers) SpoonHTTP::setHeaders(self::ATOM_HEADER . $this->getCharset());

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
	 * Reads an feed into a SpoonAtom object
	 *
	 * @return	SpoonAtom
	 * @param	string $url
	 * @param	string[optional] $type
	 */
	public static function readFromFeed($url, $type = 'url')
	{
		$aAllowedTypes = array('url', 'string');

		// redefine var
		$url = (string) $url;
		$type = (string) SpoonFilter::getValue($type, $aAllowedTypes, 'url');

		// validate
		if(!in_array($type, $aAllowedTypes)) throw new SpoonAtomException('This ('. $type .') isn\'t allowed. Only '. join(', ', $aAllowedTypes) .' are allowed.');
		if($type == 'url' && !SpoonFilter::isURL($url)) throw new SpoonAtomException('This ('. SpoonFilter::htmlentities($url) .') isn\'t a valid url.');
		if(!self::isValid($url, $type)) throw new SpoonAtomException('Invalid feed');

		// load xmlstring
		if($type == 'url')
		{
			// check if allow_url_fopen is enabled
			if(ini_get('allow_url_fopen') == 0) throw new SpoonAtomException('allow_url_fopen should be enabled, if you want to get a remote url.');

			// open the url
			$handle = @fopen($url, 'r');

			// validate the handle
			if($handle === false) throw new SpoonAtomException('Something went wrong while retrieving the url.');

			// read the string
			$xmlString = @stream_get_contents($handle);

			// close the hanlde
			@fclose($handle);
		}

		// not that url
		else $xmlString = $url;

		// convert to simpleXML
		$xml = @simplexml_load_string($xmlString, null, LIBXML_NOCDATA);

		// validate the feed
		if($xml === false) throw new SpoonAtomException('Invalid atom-string.');

		// get title, link and description
		$id = utf8_decode((string) $xml->id);
		$title = utf8_decode((string) $xml->title);
		$titleType = (isset($xml->title['type'])) ? (string) $xml->title['type'] : 'text';
		$updated = strtotime((string) $xml->updated);

		// create instance
		$atom = new SpoonAtom($id, $title, $titleType, $updated);

		// generator available?
		if(isset($xml->generator) && $xml->generator != '')
		{
			// get variables
			$name = utf8_decode((string) $xml->generator);
			$uri = (isset($xml->generator['uri']) && $xml->generator['uri'] != '') ? (string) $xml->generator['uri'] : null;
			$version = (isset($xml->generator['version']) && $xml->generator['version'] != '') ? utf8_decode((string) $xml->generator['version']) : null;

			// set generator
			$atom->setGenerator($name, $uri, $version);
		}

		// icon available
		if(isset($xml->icon)) $atom->setIcon(utf8_decode((string) $xml->icon));

		// logo available
		if(isset($xml->logo)) $atom->setLogo(utf8_decode((string) $xml->logo));

		// rights available
		if(isset($xml->rights))
		{
			// get variables
			$text = utf8_decode((string) $xml->rights);
			$type = (isset($xml->rights['type'])) ? (string) $xml->rights['type'] : 'text';
			$src= (isset($xml->rights['src'])) ? (string) $xml->rights['src'] : null;

			// set rights
			$this->setRights($text, $type, $src);
		}

		// subtitle available?
		if(isset($xml->subtitle) && $xml->subtitle != '')
		{
			// get variables
			$value = (string) utf8_decode($xml->subtitle);
			$type = (isset($xml->subtitle['type']) && $xml->subtitle['type'] != '') ? (string) $xml->subtitle['type'] : 'text';
			$src = (isset($xml->subtitle['src']) && $xml->subtitle['src'] != '') ? (string) $xml->subtitle['src'] : null;

			// set subtitle
			$atom->setSubtitle($value, $type, $src);
		}

		// author available?
		if(isset($xml->author))
		{
			// loop authors
			foreach($xml->author as $author)
			{
				// get variables
				$name = utf8_decode((string) $author->name);
				$uri = (isset($author->uri) && $author->uri != '') ? (string) $author->uri : null;
				$email = (isset($author->email) && $author->email != '') ? (string) $author->email : null;

				// add author
				$atom->addAuthor($name, $uri, $email);
			}
		}

		// categories available?
		if(isset($xml->category))
		{
			// loop categories
			foreach($xml->category as $category)
			{
				// get variables
				$term = utf8_decode((string) $category['term']);
				$scheme = (isset($category['scheme'])) ? utf8_decode((string) $category['scheme']) : null;
				$label = (isset($category['label'])) ? utf8_decode((string) $category['label']) : null;

				// add category
				$this->addCategory($term, $scheme, $label);
			}
		}

		// author available?
		if(isset($xml->contributor))
		{
			// loop authors
			foreach($xml->contributor as $contributor)
			{
				// get variables
				$name = utf8_decode((string) $contributor->name);
				$uri = (isset($contributor->uri) && $contributor->uri != '') ? (string) $contributor->uri : null;
				$email = (isset($contributor->email) && $contributor->email != '') ? (string) $contributor->email : null;

				// add contributor
				$atom->addContributor($name, $uri, $email);
			}
		}

		// links present?
		if(isset($xml->link))
		{
			// loop links
			foreach($xml->link as $link)
			{
				// get variables
				$href = (string) utf8_decode($link['href']);
				$rel = (isset($link['rel'])) ? utf8_decode((string) $link['rel']) : null;
				$type = (isset($link['type'])) ? utf8_decode((string) $link['type']) : null;
				$title = (isset($link['title'])) ? utf8_decode((string) $link['title']) : null;
				$length = (isset($link['length'])) ? utf8_decode((int) $link['length']) : null;
				$hreflang = (isset($link['hreflang'])) ? utf8_decode((string) $link['hreflang']) : null;

				// add link
				$atom->addLink($href, $rel, $type, $title, $length, $hreflang);
			}
		}

		// entries?
		if(isset($xml->entry))
		{
			// loop entries
			foreach($xml->entry as $entry)
			{
				// try to read
				try
				{
					// read xml
					$item = SpoonAtomEntry::readFromXML($entry);
					$atom->addEntry($item);
				}

				// catch exceptions
				catch (Exception $e)
				{
					// ignore exceptions
				}
			}
		}

		// return
		return $atom;
	}


	/**
	 * Identifies the software used to generate the feed
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $uri
	 * @param	string[optional] $version
	 */
	public function setGenerator($name, $uri = null, $version = null)
	{
		$aGenerator['name'] = (string) $name;
		if($uri !== null) $aGenerator['uri'] = (string) $uri;
		if($version !== null) $aGenerator['version'] = (string) $version;

		$this->aGenerator = $aGenerator;
	}


	/**
	 * Set the icon that provides iconic visual identification for the feed
	 * Icons should be square
	 *
	 * @return	void
	 * @param	string $url
	 */
	public function setIcon($url)
	{
		$this->icon = (string) $url;
	}


	/**
	 * Set an id that identifies the feed using a universally uniqie and permanent URI.
	 * If you have a long-term, renewable lease on your Internet domain name, then you can feel free to use your website's address.
	 *
	 * @return	void
	 * @param	string $id
	 */
	public function setId($id)
	{
		$this->id = (string) $id;
	}


	/**
	 * Set the logo that provides visual identification for the feed
	 * Images should be twice as wide as they are small
	 *
	 * @return	void
	 * @param	string $url
	 */
	public function setLogo($url)
	{
		$this->logo = (string) $url;
	}


	/**
	 * Set the rights for the feed
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
	 * Set sorting status
	 *
	 * @return	void
	 * @param	bool[optional] $sorting
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
	 * Set the subtitle for the feed
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
	public function setSubtitle($text, $type = 'text', $src = null)
	{
		// create array
		$aSubtitle['value'] = (string) $text;
		if($type !== null) $aSubtitle['type'] = (string) $type;
		if($src !== null) $aSubtitle['src'] = (string) $src;

		// set property
		$this->aSubtitle = $aSubtitle;
	}


	/**
	 * Set a human readable title for the feed
	 * @todo	add type
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

		// set property
		$this->title = array('type' => $type, 'value' => $title);
	}


	/**
	 * Set the last time the feed was modified in a significant way
	 *
	 * @return	void
	 * @param	int $timestamp
	 */
	public function setUpdated($timestamp)
	{
		$this->updated = (int) $timestamp;
	}


	/**
	 * Sort the item on publication date
	 *
	 * @return	void
	 */
	private function sort()
	{
		// get items
		$entries = $this->getEntries();

		// sort
		uasort($entries, array('SpoonAtom', 'compareObjects'));

		// set items
		$this->entries = $entries;
	}

}

?>
