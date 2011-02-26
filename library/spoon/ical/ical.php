<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	ical
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.3.0
 */


/**
 * This base class provides all the methods used by iCal-files
 *
 * @package		spoon
 * @subpackage	feed
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.1.0
 */
class SpoonIcal
{
	/**
	 * The calendar scale
	 *
	 * @var	string
	 */
	private $calendarScale = 'GREGORIAN';


	/**
	 * Items
	 *
	 * @var	array
	 */
	private $items = array();


	/**
	 * The method
	 *
	 * @var	string
	 */
	private $method;


	/**
	 * The product identifier
	 *
	 * @var	string
	 */
	private $productIdentifier;


	/**
	 * The version
	 *
	 * @var	string
	 */
	private $version = '2.0';


	/**
	 * The properties
	 *
	 * @var array
	 */
	private $xProperties;


	/**
	 * Add an event
	 *
	 * @return	void
	 * @param	SpoonIcalItem $item		The item to add.
	 */
	public function addItem(SpoonIcalItem $item)
	{
		$this->items[] = $item;
	}


	/**
	 * Build the ical
	 *
	 * @return	string	A string that represents the fully build iCal.
	 */
	protected function buildICal()
	{
		// init var
		$string = '';

		// start string
		$string .= 'BEGIN:VCALENDAR'."\n";

		// set version
		$string .= 'VERSION:'. $this->getVersion() ."\n";

		// set product identifier
		$string .= 'PRODID:'. $this->getProductIdentifier() ."\n";
		$string .= 'CALSCALE:'. $this->getCalendarScale() ."\n";
		if($this->getMethod() != '') $string .= 'METHOD:'. $this->getMethod() ."\n";

		// get extensions
		$xProperties = $this->getXProperties();

		// any extensions?
		if(!empty($xProperties))
		{
			// loop
			foreach($xProperties as $key => $value) $string .= $key .':'. $value ."\n";
		}

		// loop all events
		foreach($this->getItems() as $item) $string .= $item->parse() ."\n";

		// end string
		$string .= 'END:VCALENDAR';

		// return
		return $string;
	}


	/**
	 * Get the calendar scale
	 *
	 * @return	string
	 */
	public function getCalendarScale()
	{
		return $this->calendarScale;
	}


	/**
	 * Get all event-objects
	 *
	 * @return	array
	 */
	public function getItems()
	{
		return (array) $this->items;
	}


	public function getMethod()
	{
		return $this->method;
	}


	/**
	 * Get product indentifier
	 *
	 * @return	string
	 */
	public function getProductIdentifier()
	{
		return '-//Spoon v'. SPOON_VERSION .'//'. $this->productIdentifier;
	}


	/**
	 * Get version
	 *
	 * @return	string
	 */
	public function getVersion()
	{
		return $this->version;
	}


	public function getXProperties()
	{
		return $this->xProperties;
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
		if((bool) $headers) SpoonHTTP::setHeaders(self::HEADER . $this->getCharset());

		// return
		echo $this->buildIcal();

		// stop here
		exit;
	}


	/**
	 * Write the ical into a file
	 *
	 * @return	void
	 * @param	string $path	The path (and filename) where the ical should be written.
	 */
	public function parseToFile($path)
	{
		// get xml
		$ical = $this->buildICal();

		// write content
		SpoonFile::setContent((string) $path, $ical, false, true);
	}


	/**
	 * Set the scale.
	 *
	 * @return	void
	 * @param	string $scale	The scale.
	 */
	public function setCalendarScale($scale)
	{
		$this->scale = (string) $scale;
	}


	/**
	 * The method
	 *
	 * @return	void
	 * @param	string $method		The method of the calendar.
	 */
	public function setMethod($method)
	{
		$this->method = (string) $method;
	}


	/**
	 * Set product identifier
	 *
	 * @return	void
	 * @param	string $value		The product identifier, our will be prepended.
	 */
	public function setProductIdentifier($value)
	{
		$this->prodId = (string) $value;
	}


	/**
	 * Set version
	 *
	 * @return	void
	 * @param	string[optional] $value		The version.
	 */
	public function setVersion($value = '2.0')
	{
		$this->version = (string) $value;
	}


	/**
	 * Set the X-properties
	 *
	 * @return	void
	 * @param 	array $properties	The properties as a key-value-pairs.
	 */
	public function setXProperties(array $properties)
	{
		foreach($properties as $key => $value) $this->xProperties[(string) $key] = $value;
	}

}

?>