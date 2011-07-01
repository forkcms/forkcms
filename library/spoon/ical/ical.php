<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	http
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.3.2
 */


/**
 * This class is used to handle iCal-feeds
 *
 * @package		spoon
 * @subpackage	ical
 *
 * @todo	x-prop, iana-prop
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.3.2
 */
class SpoonICal
{
	private $calendarScale = 'GREGORIAN';

	/**
	 * An array of the events
	 *
	 * @var	array
	 */
	private $events;


	private $fileName = 'ical.ics';
	private $method;
	private $productIdentifier = '-//Spoon Ical//';
	private $version = '2.0';


	/**
	 * Add an event
	 *
	 * @param	SpoonIcalEvent $event	The event object
	 */
	public function addEvent(SpoonIcalItem $event)
	{
		$this->events[] = $event;
	}


	private function build()
	{
		// start
		$string = 'BEGIN:VCALENDAR' . "\n";

		// product identifier
		$string .= 'PRODID:' . $this->productIdentifier . "\n";

		// version
		$string .= 'VERSION:' . $this->version . "\n";

		// calscale	optional single
		if($this->calendarScale !== null) $string .= 'CALSCALE:' . $this->calendarScale . "\n";

		// method	optional single
		if($this->method !== null) $string .= 'METHOD:' . $this->method . "\n";


		foreach($this->events as $item)
		{
			$string .= $item->build();
		}

		// end
		$string .= 'END:VCALENDAR' . "\n";

		// return
		return $string;
	}


	/**
	 * Get all events
	 *
	 * @return	array
	 */
	public function getEvents()
	{
		return (array) $this->events;
	}


	public function getFilename()
	{
		return (string) $this->fileName;
	}


	public function getProductIdentifier()
	{
		return $this->productIdentifier;
	}


	public function getVersion()
	{
		return $this->version;
	}


	/**
	 * Parse the feed and output the feed into the browser.
	 *
	 * @return	void
	 * @param	bool[optional] $headers		Should the headers be set? (Use false if you're debugging).
	 */
	public function parse($headers = true)
	{
		// set headers
		if((bool) $headers)
		{
			SpoonHTTP::setHeaders('Content-Type: text/Calendar');
			SpoonHTTP::setHeaders('Content-Disposition: inline; filename='. $this->getfilename());
		}

		// output
		echo $this->build();

		// stop here
		exit;
	}


	/**
	 * Write the feed into a file
	 *
	 * @return	void
	 * @param	string $path	The path (and filename) where the feed should be written.
	 */
	public function parseToFile($path)
	{
		// get xml
		$data = $this->build();

		// write content
		SpoonFile::setContent((string) $path, $data, false, true);
	}


	/**
	 * Set the filename.
	 *
	 * @return	void
	 * @param	string $name	The name of the file.
	 */
	public function setFilename($name)
	{
		$this->fileName = (string) $name;
	}


	/**
	 * Set the product identifier
	 *
	 * @return	void
	 * @param	string $value	The product identifier.
	 */
	public function setProductIdentifier($value)
	{
		$this->productIdentifier = (string) $value;
	}
}


/*
 * @todo	find a way to set those params
 */
class SpoonICalItem
{
	protected $class;
	protected $created;
	protected $description;
	protected $dtStamp;
	protected $dtStart;
	protected $duration;
	protected $geo;
	protected $lastModified;
	protected $location;
	protected $summary;
	protected $uid;


	/**
	 * This property defines the access classification for a calendar component.
	 *
	 * @return	void
	 * @param	 $value		The access classification, eg: PUBLIC, PRIVATE, CONFIDENTIAL, ...
	 */
	public function setClass($value)
	{
		$this->class = (string) $value;
	}


	/**
	 * This property specifies the date and time that the calendar information was created by the calendar user agent in the calendar store.
	 *
	 * @return	void
	 * @param	int $value	The timestamp.
	 */
	public function setCreated($value)
	{
		$this->created = (int) $value;
	}


	/**
	 * This property provides a more complete description of the calendar component than that provided by the "SUMMARY" property.
	 *
	 * @return	void
	 * @param	string $value	The description
	 */
	public function setDescription($value)
	{
		$this->description = (string) $value;
	}


	/**
	 * In the case of an iCalendar object that specifies a "METHOD" property, this property specifies the date and time that the instance of the iCalendar object was created.
	 * In the case of an iCalendar object that doesn't specify a "METHOD" property, this property specifies the date and time that the information associated with the calendar component was last revised in the calendar store.
	 *
	 * @return	void
	 * @param	int $value	The timestamp.
	 */
	public function setDTStamp($value)
	{
		$this->dtStamp = (int) $value;
	}


	/**
	 * This property specifies when the calendar component begins.
	 *
	 * @return	void
	 * @param	int $value	The timestamp.
	 */
	public function setDTStart($value)
	{
		$this->dtStart = (int) $value;
	}


	/**
	 * This property specifies a positive duration of time.
	 *
	 * @return	void
	 * @param unknown_type $value
	 */
	public function setDuration($value)
	{
		$this->duration = (string) $duration;
	}


	/**
	 * This property specifies information related to the global position for the activity specified by a calendar component.
	 *
	 * @return	void
	 * @param	float $lat
	 * @param	float $lon
	 */
	public function setGeo($lat, $lon)
	{
		$this->geo['lat'] = (float) $lat;
		$this->geo['lon'] = (float) $lon;
	}


	/**
	 * This property specifies the date and time that the information associated with the calendar component was last revised in the calendar store.
	 *
	 * @return	void
	 * @param	int $value	The timestamp.
	 */
	public function setLastModified($value)
	{
		$this->lastModified = (int) $value;
	}


	/**
	 * This property defines the intended venue for the activity defined by a calendar component.
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setLocation($value)
	{
		$this->location = (string) $value;
	}


	/**
	 * This property defines a short summary or subject for the calendar component.
	 *
	 * @return	void
	 * @param	string $value	The summary.
	 */
	public function setSummary($value)
	{
		$this->summary = (string) $value;
	}


	public function setUID($value)
	{
		$this->uid = (string) $value;
	}

}


/**
 * This class is used to handle iCal-items
 *
 * @package		spoon
 * @subpackage	ical
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.3.2
 */
class SpoonICalEvent extends SpoonICalItem
{
	public function build()
	{
		// start
		$string = 'BEGIN:VEVENT' . "\n";

	// required
		// dtstrart
		if($this->dtStamp == null) $this->dtStamp = time();
		$string .= 'DTSTAMP;TZID=' . date_default_timezone_get() . ':' .  date('Ymd\THis', $this->dtStamp) . "\n";

		// uid
		if($this->uid == '') throw new SpoonIcalException('No UID set.');
		$string .= 'UID:' . $this->uid . "\n";

	// if method is not set this is required
		// dtstart
		if($this->dtStart !== null) $string .= 'DTSTART;TZID=' . date_default_timezone_get() . ':' .  date('Ymd\THis', $this->dtStart) . "\n";

	// optional (single)
		// class
		if($this->class !== null) $string .= 'CLASS:' . $this->class . "\n";

		// created
		if($this->created !== null) $string .= 'CREATED;TZID=' . date_default_timezone_get() . ':' .  date('Ymd\THis', $this->created) . "\n";

		// description
		if($this->description !== '')
		{
			$value = trim($this->description);
			$value = str_replace(array("\n"), array('\n'), $value);
			$value = str_replace(array("\n", "\r"), '', $value);

			$string .= 'DESCRIPTION:' . $value . "\n";
		}

		// geo
		if(isset($this->geo['lat']) && isset($this->geo['lon'])) $string .= 'GEO:' . $this->geo['lat'] . ';' . $this->geo['lon'] . "\n";

        // last-mod
        if($this->lastModified !== null) $string .= 'LAST-MODIFIED;TZID=' . date_default_timezone_get() . ':' .  date('Ymd\THis', $this->dtStart) . "\n";

        // location
        if($this->location !== null)
        {
			$value = trim($this->location);
			$value = str_replace(array("\n"), array('\n'), $value);
			$value = str_replace(array("\n", "\r"), '', $value);

        	$string .= 'LOCATION:' . $value . "\n";
        }

        // organizer
        // priority
        // seq
        // status
        // summary
        if($this->summary !== null) $string .= 'SUMMARY:' . $this->summary . "\n";

        // transp
        // url
        // recurid

	// single optional
		// dtend

		// duration

	// multiple optional
		// rrule

	// multiple optional
		// attach
		// attendee
		// categories
		// comment
		// contact
		// exdate
		// rstatus
		// related
		// resources
		// rdate
		// x-prop
		// iana-prop

		// end
		$string .= 'END:VEVENT' . "\n\n";

		// return
		return $string;
	}
}


/**
 * This exception is used to handle iCal related exceptions.
 *
 * @package		spoon
 * @subpackage	ical
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.3.2
 */
class SpoonIcalException extends SpoonException {}

?>