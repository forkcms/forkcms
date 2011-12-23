<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			webservices
 * @subpackage		icalendar
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** Spoon class */
require_once 'spoon/spoon.php';

/** Spoon ICalendar execption class */
require_once 'spoon/webservices/icalendar/exception.php';

/** Spoon HTTP class */
require_once 'spoon/http/http.php';


/**
 * This base class provides all the methods used by iCalendar
 *
 * @package			webservices
 * @subpackage		icalendar
 *
 * @author 			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */
class SpoonICalendarEvent
{
	/**
	 * Create date
	 *
	 * @var	int
	 */
	private $createDate;


	/**
	 * Description
	 *
	 * @var	string
	 */
	private $description;


	/**
	 * End date
	 *
	 * @var	int
	 */
	private $endDate;


	/**
	 * Location
	 *
	 * @var	string
	 */
	private $location;


	/**
	 * Start date
	 *
	 * @var	int
	 */
	private $startDate;


	/**
	 * Summary
	 *
	 * @var	string
	 */
	private $summary;


	/**
	 * Unique Identifier
	 *
	 * @var	string
	 */
	private $uid;


	/**
	 * Url
	 *
	 * @var string
	 */
	private $url;


	/**
	 * Default constructor
	 *
	 * @param	int $startDate
	 * @param	int $endDate
	 * @param	int $description
	 * @param	int $createDate
	 */
	public function __construct($summary, $description, $startDate, $endDate = null, $createDate = null)
	{
		// set summary
		$this->setSummary($summary);

		// set description
		$this->setDescription($description);

		// set start date
		$this->setStartDate($startDate);

		// set end date
		$this->setEndDate($endDate);

		// set create date
		if($createDate === null) $this->setCreateDate(time());
		else $this->setCreateDate($createDate);

		// set uid
		$this->setUID(null);
	}


	/**
	 * Set creation date
	 *
	 * @return	int
	 */
	public function getCreateDate()
	{
		return $this->createDate;
	}


	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}


	/**
	 * Get end date
	 *
	 * @return	int
	 */
	public function getEndDate()
	{
		return	$this->endDate;
	}


	/**
	 * Get location
	 *
	 * @return	string
	 */
	public function getLocation()
	{
		return $this->location;
	}


	/**
	 * Get start date
	 *
	 * @return	int
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}


	/**
	 * Get Summary
	 *
	 * @return	string
	 */
	public function getSummary()
	{
		return $this->summary;
	}


	/**
	 * Get unique identifier
	 *
	 * @return	string
	 */
	public function getUID()
	{
		return $this->uid;
	}


	/**
	 * Get url
	 *
	 * @return	string
	 */
	public function getUrl()
	{
		return $this->url;
	}


	/**
	 * Parse this event
	 *
	 * @return	string
	 */
	public function parse()
	{
		// init string
		$string = '';

		// begin
		$string .= 'BEGIN:VEVENT'."\n";

		// add creation date
		$string .= 'CREATED:' . date('Ymd\THis', $this->getCreateDate()) ."\n";
		$string .= 'DTSTAMP:' . date('Ymd\THis', $this->getCreateDate()) ."\n";

		// add UID
		$string .= 'UID:' . $this->getUID() . "\n";

		// add summary
		$string .= 'SUMMARY:'. $this->getSummary() ."\n";

		// add description if needed
		if($this->description !== null) $string .= 'DESCRIPTION:' . $this->getDescription() . "\n";

		// add url if needed
		if($this->getUrl() !== null) $string .= 'URL:' . $this->getUrl() . "\n";

		// add startdate
		$string .= 'DTSTART:' . date('Ymd\THis', $this->getStartDate()). "\n";

		// add enddate if needed
		if($this->getEndDate() !== null) $string .= 'DTEND:' . date('Ymd\THi00', $this->getEndDate()) . "\n";

		// add location if needed
		if($this->getLocation() !== null) $string .= 'LOCATION:' . $this->getLocation() ."\n";

		// end
		$string .= 'END:VEVENT'."\n";

		// return
		return $string;
	}


	/**
	 * Set creation date
	 *
	 * @param	int[optional] $value
	 */
	public function setCreateDate($value = null)
	{
		if($value == null) $this->createDate = time();
		else $this->createDate = (int) $value;
	}


	/**
	 * Set description
	 *
	 * @param	string $value
	 */
	public function setDescription($value)
	{
		$this->description = (string) $value;
	}


	/**
	 * Set end date
	 *
	 * @param	int $value
	 */
	public function setEndDate($value)
	{
		$this->endDate = (int) $value;
	}


	/**
	 * Set a location
	 *
	 * @param	string $value
	 */
	public function setLocation($value)
	{
		$this->location = (string) $value;
	}


	/**
	 * Set start date
	 *
	 * @param	int $value
	 */
	public function setStartDate($value)
	{
		$this->startDate = (int) $value;
	}


	/**
	 * Set summary
	 *
	 * @param	string $summary
	 */
	public function setSummary($value)
	{
		$this->summary = (string) $value;
	}


	/**
	 * Set unique identifier
	 *
	 * @param	string[optional] $value
	 */
	public function setUID($value = null)
	{
		if($value === null) $this->uid = md5(uniqid());
		else $this->uid = (string) $value;
	}


	/**
	 * Set URL
	 *
	 * @param	string $value
	 */
	public function setUrl($value)
	{
		$this->url = (string) $value;
	}
}
