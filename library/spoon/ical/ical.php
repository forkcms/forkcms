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
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Sam Tubbax <sam@sumocoders.be>
 * @since		1.3.2
 */


/**
 * This class is used to handle iCal-feeds
 * @todo	iana-properties
 * @todo	setters
 * @todo	getters
 *
 * @package		spoon
 * @subpackage	ical
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Sam Tubbax <sam@sumocoders.be>
 * @since		1.3.2
 */
class SpoonICal
{
	/**
	 * Defines the calendar scale used for the calendar
	 *
	 * @var	string
	 */
	private $calendarScale = 'GREGORIAN';


	/**
	 * An array of the events
	 *
	 * @var	array
	 */
	private $events;


	/**
	 * The default filename
	 *
	 * @var	string
	 */
	private $fileName = 'ical.ics';


	/**
	 * Defines the iCalendar object method
	 *
	 * @var string
	 */
	private $method;


	/**
	 * Specifies the identifier for the product that created the iCalendar object
	 *
	 * @var	string
	 */
	private $productIdentifier = '-//Spoon Ical//';


	/**
	 * ICAL version to use
	 *
	 * @var	string
	 */
	private $version = '2.0';


	/**
	 * The timezone
	 *
	 * @var	string
	 */
	private $timezone;


	/**
	 * The X-properties
	 *
	 * @var	array
	 */
	private $xProperties;


	/**
	 * Add an event
	 *
	 * @param	SpoonIcalEvent $event	The event object
	 */
	public function addEvent(SpoonIcalItem $event)
	{
		$this->events[] = $event;
	}


	/**
	 * Build the item (as in: convert it to a string)
	 *
	 * @return	string
	 */
	private function build()
	{
		// start
		$string = 'BEGIN:VCALENDAR' . "\r\n";

		// version
		$string .= 'VERSION:' . $this->version . "\r\n";

		// product identifier
		$string .= 'PRODID:' . $this->productIdentifier . "\r\n";

		// calscale	optional single
		if($this->calendarScale !== null) $string .= 'CALSCALE:' . $this->calendarScale . "\r\n";

		// method	optional single
		if($this->method !== null) $string .= 'METHOD:' . $this->method . "\r\n";

		// x-prop
		if($this->xProperties != null)
		{
			foreach($this->xProperties as $key => $property)
			{
				$string .= $key . ':' . $property . "\r\n";
			}
		}

		foreach($this->events as $item)
		{
			$string .= $item->build();
		}

		// end
		$string .= 'END:VCALENDAR' . "\r\n";

		// return
		return $string;
	}


	/**
	 * Formats a string for building purposes
	 *
	 * @return	string
	 * @param	string $source		The original string
	 */
	public static function formatString($source)
	{
		$text = str_replace(',', '\,', $source);
		$text = str_replace(';', '\;', $text);
        $text = trim($text);
		$text = str_replace(array("\n"), array("\n"), $text);
		$text = str_replace(array("\n", "\r"), '', $text);

		return $text;
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


	/**
	 * Get the filename
	 *
	 * @return	string
	 */
	public function getFilename()
	{
		return (string) $this->fileName;
	}


	/**
	 * Get the product identifier
	 *
	 * @return string
	 */
	public function getProductIdentifier()
	{
		return $this->productIdentifier;
	}


	/**
	 * Get the version
	 *
	 * @return string
	 */
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


	/**
	 * Set the X-properties
	 *
	 * @return	void
	 * @param	array $values	The properties
	 */
	public function setXProperties($values)
	{
		$this->xProperties = (array) $values;
 	}
}


/**
 * Base class for iCal-objects
 * @todo	docs
 * @todo	getters
 * @todo	setters
 * @todo	move custom properties to Event
 * @todo	implement VTODO en VJOURNAL
 * @todo	refactor build-methods
 *
 * @package		spoon
 * @subpackage	ical
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Sam Tubbax <sam@sumocoders.be>
 * @since		1.3.2
 */
class SpoonICalItem
{
	protected $attach;
	protected $attendees;
	protected $categories;
	protected $class;
	protected $created;
	protected $comments;
	protected $contacts;
	protected $description;
	protected $dtEnd;
	protected $dtStamp;
	protected $dtStart;
	protected $duration;
	protected $exceptions;
	protected $geo;
	protected $ianaProperties;
	protected $lastModified;
	protected $location;
	protected $organizer;
	protected $priority;
	protected $rdates;
	protected $related;
	protected $resources;
	protected $rrule;
	protected $rstatus;
	protected $sequence;
	protected $status;
	protected $summary;
	protected $transp;
	protected $recurId;
	protected $uid;
	protected $url;
	protected $xProperties;


	/**
	 * Add an attachment
	 *
	 * @return	void
	 * @param 	string[optional] $url			A url to the file.
	 * @param 	string[optional] $filePath		The Path to the file if you want to include it in the ical file.
	 * @param	string[optional] $fmType		Reccomended when using filePAth, the media type of file.
	 */
	public function addAttachment($url = null, $filePath = null, $fmtType = null)
	{
		// validate
		if($url == null && $filePath == null) throw new SpoonIcalException('Please provide either a file or url');

		// init var
		$attachment = array();

		if($url == null)
		{
			$attachment['ENCODING'] = "BASE64";
			$attachment['VALUE'] = "BINARY";
			if($fmtType !== null) $attachment['FMTTYPE'] = $fmtType;
			$attachment['content'] = base64_encode(SpoonFile::getContent($filePath));
		}
		else
		{
			$attachment['VALUE'] = 'URI';
			$attachment['content'] = $url;
		}

		// init the array if empty
		if($this->attach == null) $this->attach = array();

		$attach[] = $attachment;
	}


	/**
	 * Add an attendee
	 *
	 * @return	void
	 * @param	string $email			The email of the attendee.
	 * @param	string[optional] $name	The display name of the attendee.
	 */
	public function addAttendee($email, $name = null)
	{
		$attendee = array('email' => $email);
		if($name !== null) $attendee['CN'] = $name;


		// init the array if empty
		if($this->attendees == null) $this->attendees = array();

		$this->attendees[] = $attendee;
	}


	/**
	 * Add a category
	 *
	 * @return	void
	 * @param	string $value	The name of the category
	 */
	public function addCategory($value)
	{
		$this->categories[] = (string) $value;
	}


	/**
	 * Adds a comment
	 *
	 * @return	void
	 * @param	string $value	The comment.
	 */
	public function addComment($value)
	{
		if($this->comments == null) $this->comments = array();

		$this->comments[] = SpoonICal::formatString($value);
	}


	/**
	 * Add an exception for a recurring event
	 *
	 * @return	void
	 * @param	int $value	The timestamp.
	 */
	public function addException($value)
	{
		if($this->rrule == null) throw new SpoonIcalException('Setting an exception to a non-existing rule might not be the best idea');

		if($this->exceptions == null) $this->exceptions = array();

		$this->exceptions[] = (int) $value;
	}


	/**
	 * Adds contact information
	 *
	 * @return	void
	 * @param	string $value	The Contact info.
	 */
	public function addContact($value)
	{
		if($this->contacts == null) $this->contacts = array();

		$this->contacts[] = SpoonICal::formatString($value);
	}


	/**
	 * Add Reccuring date
	 *
	 * @return	void
	 * @param	int $value		The timestamp.
	 */
	public function addReccurrenceDate($value)
	{
		if($this->rdates == null) $this->rdates = array();

		$this->rdates[] = (int) $value;
	}


	/**
	 * Get Attachments
	 *
	 * @return	array
	 */
	public function getAttachments()
	{
		return (array) $this->attach;
	}


	/**
	 * Get attendees
	 *
	 * @return	array
	 */
	public function getAttendees()
	{
		return (array) $this->attendees;
	}


	/**
	 * Get Categories
	 *
	 * @return	array
	 */
	public function getCategories()
	{
		return (array) $this->categories();
	}


	/**
	 * Get classification
	 *
	 * @return	string
	 */
	public function getClassification()
	{
		return (string) $this->class;
	}


	/**
	 * This property specifies the date and time that the calendar information was created by the calendar user agent in the calendar store.
	 *
	 * @return	int
	 */
	public function getCreated()
	{
		return (int) $this->created;
	}


	/**
	 * Get comments
	 *
	 * @return	array
	 */
	public function getComments()
	{
		return (array) $this->comments;
	}


	/**
	 * Get the contacts
	 *
	 * @return	array
	 */
	public function getContacts()
	{
		return (array) $this->contacts;
	}


	/**
	 * Get the description
	 *
	 * @return	string
	 */
	public function getDescription()
	{
		return (string) $this->description;
	}


	/**
	 * Get the end of an event (as a timestamp)
	 *
	 * @return	int
	 */
	public function getEnd()
	{
		return (int) $this->dtEnd;
	}


	/**
	 * Get dtStamp (and that is...)?
	 * Well...
	 * In the case of an iCalendar object that specifies a "METHOD" property, this property specifies the date and time that the instance of the iCalendar object was created.
	 * In the case of an iCalendar object that doesn't specify a "METHOD" property, this property specifies the date and time that the information associated with the calendar component was last revised in the calendar store.
	 *
	 *  @return	int
	 */
	public function getDTStamp()
	{
		return (int) $this->dtStamp;
	}


	/**
	 * Get the start of an event (as a timestamp)
	 *
	 * @return	void
	 */
	public function getStart()
	{
		return (int) $this->dtStart;
	}


	/**
	 * Get the duration of the event in seconds
	 *
	 * @return int
	 */
	public function getDuration()
	{
		return (int) $this->duration;
	}


	/**
	 * Get the exceptions to the recurrence rule
	 *
	 * @return	array
	 */
	public function getExceptions()
	{
		return (array) $this->exceptions;
	}


	/**
	 * Get the geolocation of an event
	 *
	 * @return array		with indices lat & long
	 */
	public function getGeo()
	{
		return (array) $this->geo;
	}


	/**
	 * Get iana properties (whatever those are)
	 *
	 * @return	array
	 */
	public function getIANAProperties()
	{
		return (array) $this->ianaProperties;
	}


	/**
	 * Get last modified (as a timestamp)
	 *
	 * @return	int
	 */
	public function getLastModified()
	{
		return (int) $this->lastModified;
	}

	/**
	 * Get the Unique Identifier
	 *
	 * @return	string
	 */
	public function getUID()
	{
		return $this->uid;
	}


	/**
	 * Get the URL
	 *
	 * @return	string
	 */
	public function getURL()
	{
		return $this->url;
	}


	/**
	 * Get the parent items UID
	 *
	 * @return	mixed		UID of parent if set, false if not.
	 */
	public function getParent()
	{
		foreach($this->related as $rel)
		{
			if($rel['RELTYPE'] == 'PARENT') return $rel['other'];
		}

		// fallback
		return false;
	}


	/**
	 * This property provides the capability to associate a document object with a calendar component.
	 *
	 * just urls at this point
	 *
	 * @return	void
	 * @param	array $values	The attachments
	 *
	 */
	protected function setAttachments($values)
	{
		$this->atttach = (array) $values;
	}


	/**
	 * This property provides the capability to associate a document object with a calendar component.
	 * @return	void
	 * @param	array $values	The attendees
	 *
	 */
	protected function setAttendees($values)
	{
		$this->attendees = (array) $values;
	}


	/**
	 * This property defines the categories for a calendar component
	 *
	 * @return	void
	 * @param	array $values	The categories as an array of strings
	 */
	public function setCategories($values)
	{
		$this->categories = (array) $values;
 	}


 	/**
 	 * Set an item as a child of this one
 	 *
 	 * @return	void
 	 * @param	SpoonICalItem $item		The child item
 	 */
 	public function setChild($item)
 	{
		if($this->related == null) $this->related = array();

		if($item->getUID() == null) throw new SpoonIcalException('Unique ID of Child not set, please do so.');

		$this->related[] = array('other' => $item->getUID(), 'RELTYPE' => 'CHILD');

		// set this as the items parent, if not already set
		if($item->getParent() === false) $item->setParent($this);
 	}


	/**
	 * This property defines the access classification for a calendar component.
	 *
	 * @return	void
	 * @param	string $value		The access classification, eg: PUBLIC, PRIVATE, CONFIDENTIAL, ...
	 */
	public function setClassification($value)
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
	 * This property specifies the date and time that a calendar component ends.
	 *
	 * @return	void
	 * @param	int $value	The timestamp.
	 */
	public function setEnd($value)
	{
		$this->dtEnd = (int) $value;
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
	public function setStart($value)
	{
		$this->dtStart = (int) $value;
	}


	/**
	 * This property specifies a positive duration of time. (in seconds)
	 *
	 * @return	void
	 * @param	int $seconds
	 */
	public function setDuration($seconds)
	{
		$this->duration = (int) $seconds;
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
	 * Set the IANA properties
	 *
	 * @return	void
	 * @param	array $values	The properties
	 */
	public function setIANAProperties($values)
	{
		$this->ianaProperties = (array) $values;
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
	 * This property defines the intended venue for the activity defined by a calendar component.
	 * (Must be email adress)
	 *
	 * @return	void
	 * @param	string $email				The organizers email.
	 * @param	string[optional] $name 		common or display name
	 * @param	string[optional] $dir		a pointer to the directory information associated with the "Organizer"
	 * @param	string[optional] $sentBy	another calendar user that is acting on behalf of the "Organizer".
	 * @param	string[optional] $language	The language.
	 */
	public function setOrganizer($email, $name = null, $dir = null, $sentBy = null, $language = null)
	{
		$this->organizer = array('email' => $email);

		// set optional parameters
		if($name !== null) $this->organizer['CN'] = $name;
		if($dir !== null) $this->organizer['DIR'] = $dir;
		if($sentBy !== null) $this->organizer['SENT-BY'] = $sentBy;
		if($language !== null) $this->organizer['LANGUAGE'] = $language;
	}


	/**
	 * Set the parent item of this one
	 *
	 * @return	void
	 * @param	SpoonIcalItem $item		The parent item.
	 */
	public function setParent($item)
	{
		if($this->related == null) $this->related = array();

		if($item->getUID() == null) throw new SpoonIcalException('Unique ID of parent not set, please do so.');

		$this->related[] = array('other' => $item->getUID(), 'RELTYPE' => 'PARENT');

		$item->setChild($this);
	}


	/**
	 * The relative priority for a calendarcomponent. (range 0 - 9)
	 *
	 * @return	void
	 * @param	int $value		The value.
	 */
	public function setPriority($value)
	{
		// validate
		if($value < 0 || $value > 9) throw new SpoonIcalException('Priority must be integer between 0 and 9');

		$this->priority = (int) $value;
	}


	/**
	 * This property defines a rule or repeating pattern for recurring events, to-dos, journal entries, or time zone definitions.
	 *
	 * Since I don't feel like writing out the rule syntax: see http://tools.ietf.org/html/rfc5545#section-3.3.10
	 *
	 * @return	void
	 * @param	string $values	The rule.
	 */
	public function setRecurrenceRule($value)
	{
		$this->rrule = (string) $value;
	}


	/**
	 *  This property defines the status code returned for a scheduling request
	 *
	 * @return	void
	 * @param	string $code						The requestStatus code
	 * @param	string[optional] $description		A short Description.
	 */
	public function setRequestStatus($code, $description = null)
	{
		if($this->rstatus == null) $this->rstatus = array();

		$status = array('code' => $code);
		if($description !== null)
		{
			$status['description'] = SpoonICal::formatString($description);
		}

		$this->rstatus[] = $status;
	}


	/**
	 * This property is used in conjunction with the "UID" and  "SEQUENCE" properties to identify a specific instance of a recurring Event,todo or journal
 	 *	The property value is the original value of the "DTSTART" property of the recurrence instance.
	 *
	 * @return	void
	 * @param	int $value	The timestamp.
	 */
	public function setRecurId($value)
	{
		$this->recurId = (int) $value;
	}


	/**
	 * This property defines the equipment or resources anticipated for an activity
	 *
	 * @return	void
	 * @param	array $values	The resources as an array of strings
	 */
	public function setResources($values)
	{
		$this->resources = (array) $values;
	}

	/**
	 * The revision sequence number of the calendar component within a sequence of revisions.
	 *
	 * @return	void
	 * @param	int $value
	 */
	public function setSequence($value)
	{
		$this->sequence = (int) $value;
	}


	/**
	 * The overall status or confirmation.
	 *
	 * Possible values:(might put this in constants at some point)
	 * for event: TENTATIVE, CONFIRMED, CANCELLED
	 * for todo : NEEDS-ACTION, COMPLETED, IN-PROCESS, CANCELLED
	 * for journal: DRAFT, FINAL, CANCELLED
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setStatus($value)
	{
		$this->status = (string) $value;
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


	/**
	 * This property defines whether or not an event is transparent to busy time searches.
	 * Possible values:
	 *
	 * OPAQUE: 		Blocks or opaque on busy time searches.
	 * TRANSPARENT: Transparent on busy time searches.
	 *
	 * @return	void
	 * @param 	string $value		The value.
	 */
	public function setTransp($value)
	{
		$this->transp = (string) $value;
	}


	/**
	 * Set the UID
	 *
	 * @return	void
	 * @param	string $value	The value.
	 */
	public function setUniqueIdentifier($value)
	{
		$this->uid = (string) $value;
	}


	/**
	 * This property defines a URL associated with the iCalendar object.
	 *
	 * @return	void
	 * @param	string $value	The value.
	 */
	public function setURL($value)
	{
		$this->url = (string) $value;
	}


	/**
	 * Set the X-properties
	 *
	 * @return	void
	 * @param	array $values	The properties
	 */
	public function setXProperties($values)
	{
		$this->xProperties = (array) $values;
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
 * @author		Sam Tubbax <sam@sumocoders.be>
 * @since		1.3.2
 */
class SpoonICalEvent extends SpoonICalItem
{
	/**
	 * Build the SpooncalEvent Object
	 *
	 * @return	string
	 */
	public function build()
	{
		// start
		$string = 'BEGIN:VEVENT' . "\r\n";

		// required
		// dtstrart
		if($this->dtStamp == null) $this->dtStamp = time();
		$string .= 'DTSTAMP:' . SpoonDate::getDate('Ymd\THis', $this->dtStamp, 'en', true) . "z\r\n";

		// uid
		if($this->uid == '') throw new SpoonIcalException('No UID set.');
		$string .= 'UID:' . $this->uid . "\r\n";

		// if method is not set this is required
		// dtstart
		if($this->dtStart !== null) $string .= 'DTSTART:' . SpoonDate::getDate('Ymd\THis', $this->dtStart, 'en', true) . "Z\r\n";

		// optional (single)
		// class
		if($this->class !== null) $string .= 'CLASS:' . $this->class . "\r\n";

		// created
		if($this->created !== null) $string .= 'CREATED:' . SpoonDate::getDate('Ymd\THis', $this->created, 'en', true) . "Z\r\n";

		// description
		if($this->description !== '')
		{
			$value = trim($this->description);
			$value = str_replace(array("\n"), array("\n"), $value);
			$value = str_replace(array("\n", "\r"), '', $value);

			$string .= 'DESCRIPTION:' . $value . "\r\n";
		}

		// geo
		if(isset($this->geo['lat']) && isset($this->geo['lon'])) $string .= 'GEO:' . $this->geo['lat'] . ';' . $this->geo['lon'] . "\r\n";

        // last-mod
        if($this->lastModified !== null) $string .= 'LAST-MODIFIED:' . SpoonDate::getDate('Ymd\THis', $this->lastModified, 'en', true) . "Z\r\n";

        // location
        if($this->location !== null)
        {
			$value = trim($this->location);
			$value = str_replace(array("\n"), array("\n"), $value);
			$value = str_replace(array("\n", "\r"), '', $value);

        	$string .= 'LOCATION:' . $value . "\r\n";
        }

        // organizer
        if($this->organizer !== null)
        {
        	$organizer = 'ORGANIZER';

        	// set optional params
        	foreach($this->organizer as $key=>$value)
        	{
        		if($key != 'email') $organizer .= ';' . $key . '=' . $value;
        	}

        	// set email
        	$organizer .= ':mailto:' . $this->organizer['email'];


        	$value = trim($organizer);
			$value = str_replace(array("\n"), array("\n"), $value);
			$value = str_replace(array("\n", "\r"), '', $value);

        	$string .=  $value . "\r\n";
        }

        // priority
        if($this->priority !== null) $string .= 'PRIORITY:' . $this->priority . "\r\n";

		// seq
        if($this->sequence !== null) $string .= 'SEQUENCE:' . $this->sequence . "\r\n";

        // status
		if($this->status !== null)
        {
			$value = trim($this->status);

        	$string .= 'STATUS:' . $value . "\r\n";
        }

        // summary
        if($this->summary !== null) $string .= 'SUMMARY:' . $this->summary . "\r\n";

        // transp
        if($this->transp !== null) $string .= 'TRANSP:' . $this->transp . "\r\n";

        // url
        if($this->url !== null) $string .= 'URL:' . $this->url . "\r\n";

        // recurid
        if($this->recurId !== null)
        {
        	$string .= 'RECURRENCE-ID:' . SpoonDate::getDate('Ymd\THis', $this->recurId, 'en', true) . "Z\r\n";
        }

        // if dtend AND duration are set. FREAK THE FUCK OUT
		if($this->dtEnd !== null && $this->duration !== null)
		{
			throw new SpoonIcalException('Either Duration or DateEnd can be set. Not both');
		}

		// dtend
		if($this->dtEnd !== null)
		{
			$string .= 'DTEND:' . SpoonDate::getDate('Ymd\THis', $this->dtEnd, 'en', true) . "Z\r\n";
		}

		// duration
		if($this->duration !== null)
		{
			$string .= 'DURATION:PTS' . $this->duration . "\r\n";
		}

		// rrule
		if($this->rrule !== null) $string .= 'RRULE:' . $this->rrule . "\r\n";

		// multiple optional
		// attach
		if($this->attach !== null)
		{
			foreach($this->attach as $attachment)
			{
				$string .= 'ATTACH';
				if($attachment['VALUE'] == 'URI') $string .= ':' . $attachment['content'] . "\r\n";
				else {
					if($attachment['FMTTYPE'] !== null) $string .= ';FMTTYPE=' . $attachment['FMTTYPE'];
					$string .= ';ENCODING=' . $attachment['ENCODING'];
					$string .= ';VALUE=' . $attachment['BINARY'];
					$string .= ':' . $attachment['content'];
				}
			}
		}

		// attendee
		if($this->attendees !== null)
		{
			foreach($this->attendees as $attendee)
			{
				$string .= 'ATTENDEE';
				if($attendee['CN'] != null) $string .= ';CN=' . $attendee['CN'];
				$string .= ':mailto:' . $attendee['email'];
			}
		}

		// categories
		if($this->categories !== null)
		{
			$string .= 'CATEGORIES:' . implode(',', $this->categories) . "\r\n";
		}

		// comment
		if($this->comments !== null)
		{
			foreach($this->comments as $comment)
			{
				$string .= 'COMMENT:' . $comment . "\r\n";
			}
		}

		// contact
		if($this->contacts !== null)
		{
			foreach($this->contacts as $contact)
			{
				$string .= 'CONTACT:' . $contact . "\r\n";
			}
		}

		// exdate
		if($this->exceptions !== null)
		{
			$exceptionDates = array();
			foreach($this->exceptions as $exception)
			{
				$exceptionDates[] = SpoonDate::getDate('Ymd\THis', $exception, 'en', true) . 'Z';
			}

			$string .= 'EXDATE:' . implode(',', $exceptionDates) . "\r\n";
		}

		// rstatus
		if($this->rstatus !== null)
		{
			foreach($this->rstatus as $requestStatus)
			{
				$string .= 'REQUEST-STATUS:' . $requestStatus['code'];
				if($requestStatus['description'] !== null) $string .= ';' . $requestStatus['description'];
				$string .= "\n";
			}

		}

		// related
		if($this->related !== null)
		{
			foreach($this->related as $relationship)
			{
				$string .= 'RELATED;' . $relationship['RELTYPE'] . ':' . $relationship['other'] . "\r\n";
			}
		}

		// resources
		if($this->related !== null)
		{
			$string .= 'RESOURCES:' . implode(',', $this->resources). "\r\n";
		}


		// rdate
		if($this->rdates !== null)
		{
			$reccurrenceDates = array();
			foreach($this->rdates as $rdate) $reccurrenceDates[] = SpoonDate::getDate('Ymd\THis', $rdate, 'en', true) . 'Z';

			$string .= 'RDATE:' . implode(',', $reccurrenceDates) . "\r\n";
		}

		// x-prop
		if($this->xProperties != null)
		{
			foreach($this->xProperties as $key => $property)
			{
				$string .= $key . ':' . $property . "\r\n";
			}
		}

		// iana-prop
		if($this->ianaProperties != null)
		{
			foreach($this->ianaProperties as $key => $property)
			{
				$string .= $key . ':' . $property . "\r\n";
			}
		}

		// end
		$string .= 'END:VEVENT' . "\r\n";

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