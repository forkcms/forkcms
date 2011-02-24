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
	 * Items
	 *
	 * @var	array
	 */
	private $items = array();


	/**
	 * Method
	 *
	 * @var	string
	 */
	private $method = 'PUBLISH';


	/**
	 * Product Identifier
	 *
	 * @var	string
	 */
	private $prodId;


	/**
	 * Scale
	 *
	 * @var	string
	 */
	private $scale = 'GREGORIAN';


	/**
	 * Version
	 *
	 * @var	string
	 */
	private $version = '2.0';


	/**
	 * Add an even
	 *
	 * @return	void
	 * @param	SpoonICalendarEvent $item
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
		$string .= 'PRODID:-//'. $this->getProductIdentifier() .'//EN' ."\n";
		$string .= 'CALSCALE:'. $this->getScale() ."\n";
		if($this->getMethod() != '') $string .= 'METHOD:'. $this->getMethod() ."\n";

		// loop all events
		foreach($this->getItems() as $item) $string .= $ievent->parse();

		// end string
		$string .= 'END:VCALENDAR';

		// return
		return $string;
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
		if($this->prodId == '') return 'Spoon v'. SPOON_VERSION;
		return $this->prodId;
	}



	public function getScale()
	{
		return $this->scale;
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
	 * The method
	 *
	 * @return	void
	 * @param	string $method
	 */
	public function setMethod($method)
	{
		$this->method = (string) $method;
	}


	/**
	 * Set product identifier
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setProductIdentifier($value)
	{
		$this->prodId = (string) $value;
	}


	/**
	 * Set the scale.
	 *
	 * @return	void
	 * @param	string $scale	The scale.
	 */
	public function setScale($scale)
	{
		$this->scale = (string) $scale;
	}


	/**
	 * Set version
	 *
	 * @return	void
	 * @param	string[optional] $value
	 */
	public function setVersion($value = '2.0')
	{
		$this->version = (string) $value;
	}
}

?>