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
 * This base class provides all the methods used by iCal-items.
 *
 * @package		spoon
 * @subpackage	ical
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.3.0
 */
class SpoonIcalItem
{
	/**
	 * The categories
	 *
	 * @var	array
	 */
	private $categories = array();


	/**
	 * The classification
	 *
	 * @var	string
	 */
	private $classification;


	/**
	 * The comments
	 *
	 * @var	array
	 */
	private $comments = array();


	/**
	 * The contact
	 *
	 * @var	array
	 */
	private $contact = array();


	/**
	 * The description
	 *
	 * @var	string
	 */
	private $description;


	/**
	 * The creation date
	 *
	 * @var	int
	 */
	private $datetimeCreated;


	/**
	 * The end date
	 *
	 * @var	int
	 */
	private $datetimeStamp;


	/**
	 * The start date
	 *
	 * @var	int
	 */
	private $datetimeStart;


	/**
	 * The last modifief date
	 *
	 * @var	int
	 */
	private $datetimeLastModified;


	/**
	 * The organizer
	 *
	 * @var	array
	 */
	private $organizer = array();


	/**
	 * The sequence
	 *
	 * @var	int
	 */
	private $sequence;


	/**
	 * The status
	 *
	 * @var	string
	 */
	private $status;


	/**
	 * The summary
	 *
	 * @var	string
	 */
	private $summary;


	/**
	 * The unqiue identifier
	 *
	 * @var	string
	 */
	private $uniqueIdentifier;


	/**
	 * The url
	 *
	 * @var	string
	 */
	private $url;


	/**
	 * The vendor-specific properties
	 *
	 * @var	array
	 */
	private $xProperties = array();


	/**
	 * Get the categories
	 *
	 * @return	array
	 */
	public function getCategories()
	{
		return $this->categories;
	}


	/**
	 * Get the classicifation
	 *
	 * @return	string
	 */
	public function getClassification()
	{
		return $this->classification;
	}


	/**
	 * Get the comments
	 *
	 * @return	array
	 */
	public function getComments()
	{
		return $this->comments;
	}


	/**
	 * Get the contact
	 *
	 * @return	array
	 */
	public function getContact()
	{
		return $this->contact;
	}


	/**
	 * Get the creation date
	 *
	 * @return	int
	 */
	public function getDatetimeCreated()
	{
		return $this->datetimeCreated;
	}


	/**
	 * Get the last-modified-date
	 *
	 * @return	int
	 */
	public function getDatetimeLastModified()
	{
		return $this->datetimeLastModified;
	}


	/**
	 * Get the creation/revised-date
	 *
	 * @return	int
	 */
	public function getDatetimeStamp()
	{
		return $this->datetimeStamp;
	}


	/**
	 * Get the start date
	 *
	 * @return	int
	 */
	public function getDatetimeStart()
	{
		return $this->datetimeStart;
	}


	/**
	 * Get the description
	 *
	 * @return	string
	 */
	public function getDescription()
	{
		return $this->description;
	}


	/**
	 * Get the organizer
	 *
	 * @return	array
	 */
	public function getOrganizer()
	{
		return $this->organizer;
	}


	/**
	 * Get the sequence
	 *
	 * @return	int
	 */
	public function getSequence()
	{
		return $this->sequence;
	}


	public function getStatus()
	{
		return $this->status;
	}


	/**
	 * Get the summary
	 *
	 * @return	string
	 */
	public function getSummary()
	{
		return $this->summary;
	}


	/**
	 * Get the unique identifier
	 *
	 * @return	string
	 */
	public function getUniqueIdentifier()
	{
		return $this->uniqueIdentifier;
	}


	/**
	 * Get the URL
	 *
	 * @return	string
	 */
	public function getUrl()
	{
		return $this->url;
	}


	/**
	 * Get the properties
	 *
	 * @return	array
	 */
	public function getXProperties()
	{
		return $this->xProperties;
	}


	/**
	 * Set the categories
	 *
	 * @return	void
	 * @param	array $categories	The categories.
	 */
	public function setCategories(array $categories)
	{
		$this->categories = $categories;
	}


	/**
	 * Set the classification
	 *
	 * @return	void
	 * @param	string $classification		The classification for an object.
	 */
	public function setClassification($classification)
	{
		$this->classification = $$classification;
	}


	/**
	 * Set the comments
	 *
	 * @return	void
	 * @param	array $comments		The comments.
	 */
	public function setComments(array $comments)
	{
		$this->comments = $comments;
	}


	/**
	 * Set the contact
	 *
	 * @return	void
	 * @param	string $contact				Textual contact information.
	 * @param	string[optional] $uri		URI to alternate representation.
	 */
	public function setContact($contact, $uri = null)
	{
		$this->contact['value'] = (string) $contact;
		$this->contact['uri'] = $uri;
	}


	/**
	 * Set the date created
	 *
	 * @return	void
	 * @param	int $timestamp		The creation date.
	 */
	public function setDatetimeCreated($timestamp)
	{
		$this->datetimeCreated = (int) $timestamp;
	}


	/**
	 * Set the last modified date
	 *
	 * @return	void
	 * @param	int $timestamp		The modified date.
	 */
	public function setDatetimeLastModified($timestamp)
	{
		$this->datetimeLastModified = (int) $timestamp;
	}


	/**
	 * Set the date the instance was created/last revised
	 *
	 * @return	void
	 * @param	int $timestamp		The creation date.
	 */
	public function setDatetimeStamp($timestamp)
	{
		$this->datetimeStamp = (int) $timestamp;
	}


	/**
	 * Set the datestart
	 *
	 * @return	void
	 * @param	int $timestamp		The start date.
	 */
	public function setDatetimeStart($timestamp)
	{
		$this->datetimeStart = (int) $timestamp;
	}


	/**
	 * Set the description
	 *
	 * @return	void
	 * @param	string $description		A more complete description.
	 */
	public function setDescription($description)
	{
		$this->description = (string) $description;
	}


	/**
	 * Set the organizer
	 *
	 * @return	void
	 * @param	string $email					Email of the organisor.
	 * @param	string[optional] $cn			The common name or display name.
	 * @param	string[optional] $dir			Points to the directory information containing information about the organizor.
	 * @param	string[optional] $sentBy		Specifies another user that acts on behalf the organisor.
	 * @param	string[optional] $language		Language for the CN-field.
	 */
	public function setOrganizer($email, $cn = null, $dir = null, $sentBy = null, $language = null)
	{
		// build
		$property = array();
		$property['email'] = (string) $email;
		$property['cn'] = ($cn !== null) ? (string) $cn : null;
		$property['dir'] = ($dir !== null) ? (string) $dir : null;
		$property['sentby'] = ($sentBy !== null) ? (string) $sentBy : null;
		$property['language'] = ($language !== null) ? (string) $language : null;

		// set property
		$this->organizer = $property;
	}


	/**
	 * Set the revision sequence
	 *
	 * @return	void
	 * @param	int $sequence		The sequence within the revisions.
	 */
	public function setSequence($sequence)
	{
		$this->sequence = (int) $sequence;
	}


	/**
	 * Set the status
	 *
	 * @return	void
	 * @param	string $status		The overall status/confirmation.
	 */
	public function setStatus($status)
	{
		$this->status = (string) $status;
	}


	/**
	 * Set the summary
	 *
	 * @return	void
	 * @param	string $summary		A short summary.
	 */
	public function setSummary($summary)
	{
		$this->summary = (string) $summary;
	}


	/**
	 * Set the unique identifier
	 *
	 * @return	void
	 * @param	string $identifier	The identifier.
	 */
	public function setUniqueIdentifier($identifier)
	{
		$this->uniqueIdentifier = (string) $identifier;
	}


	/**
	 * Set the url
	 *
	 * @return	void
	 * @param	string $url		The url to assiociate the item with.
	 */
	public function setUrl($url)
	{
		$this->url = (string) $url;
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


/**
 * This base class provides all the methods used by iCalEvent-items.
 *
 * @package		spoon
 * @subpackage	ical
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @since		1.3.0
 */
class SpoonIcalItemEvent extends SpoonIcalItem
{
	/**
	 * End datetime
	 *
	 * @var	int
	 */
	private $datetimeEnd;


	/**
	 * Duration
	 *
	 * @var	string
	 */
	private $duration;


	/**
	 * The GEO information
	 *
	 * @var	array
	 */
	private $geo = array();


	/**
	 * The location
	 *
	 * @var	string
	 */
	private $location;


	/**
	 * The priority
	 *
	 * @var	string
	 */
	private $priority;


	/**
	 * The needed resources
	 *
	 * @var	array
	 */
	private $resources = array();


	/**
	 * Defines if the event is transparent to busy time searches
	 *
	 * @var	string
	 */
	private $timeTransparency;


	/**
	 * Get the end datetime
	 *
	 * @return	string
	 */
	public function getDatetimeEnd()
	{
		return	$this->datetimeEnd;
	}


	/**
	 * Get the duration
	 *
	 * @return	void
	 */
	public function getDuration()
	{
		return $this->duration;
	}


	/**
	 * Get the GEO-information
	 *
	 * @return	array
	 */
	public function getGeo()
	{
		return $this->geo;
	}


	/**
	 * Get the location
	 *
	 * @return	string
	 */
	public function getLocation()
	{
		return $this->location;
	}


	/**
	 * Get the priority
	 *
	 * @return	string
	 */
	public function getPriority()
	{
		return $this->priority;
	}


	/**
	 * Get the resources
	 *
	 * @return	array
	 */
	public function getResources()
	{
		return $this->resources;
	}


	/**
	 * Get the time transparencey
	 *
	 * @return	string
	 */
	public function getTimeTransparency()
	{
		return $this->timeTransparency;
	}


	/**
	 * Parse the event to iCal-format
	 *
	 * @return	string
	 */
	public function parse()
	{
		// init var
		$string = '';

		// start string
		$string .= 'BEGIN:VEVENT'."\n";

		// categories
		$categories = $this->getCategories();
		if(!empty($categories)) $string .= 'CATEGORIES:'. implode(',', $categories) ."\n";

		// classification
		if($this->getClassification() != '') $string .= 'CLASS:'. $this->getClassification() ."\n";

		// comments
		$comments = $this->getComments();
		if(!empty($comments))
		{
			foreach($xProperties as $value) $string .= 'COMMENT:'. SpoonIcal::formatAsString($value) ."\n";
		}

		// contact
		$contact = $this->getContact();
		if(!empty($contact))
		{
			if(isset($contact['alternative'])) $string .= 'CONTACT;ALTREP="'. $contact['alternative'] .'":'. $contact['value'] ."\n";
			else $string .= 'CONTACT:'. $contact['value'] ."\n";
		}

		// datetime-created
		if($this->getDatetimeCreated() != 0) $string .= 'CREATED:'. date('Ymd\THis', $this->getDatetimeCreated()) ."\n";

		// datetime-end
		if($this->getDatetimeEnd() != 0) $string .= 'DTEND:'. date('Ymd\THis', $this->getDatetimeEnd()) ."\n";

		// lastmodified
		if($this->getDatetimeLastModified() != 0) $string .= 'LAST-MODIFIED:'. date('Ymd\THis', $this->getDatetimeLastModified()) ."\n";

		// datetimestamp
		if($this->getDatetimeStamp() != 0) $string .= 'DTSTAMP:'. date('Ymd\THis', $this->getDatetimeStamp()) ."\n";

		// datetime-start
		if($this->getDatetimeStart() != 0) $string .= 'DTSTART:'. date('Ymd\THis', $this->getDatetimeStart()) ."\n";

		// description
		if($this->getDescription() != '') $string .= 'DESCRIPTION:'. SpoonIcal::formatAsString($this->getDescription()) ."\n";

		// duration
		if($this->getDuration() != '') $string .= 'DURATION:'. $this->getDuration() ."\n";

		// geo
		$geo = $this->getGeo();
		if(!empty($geo)) $string .= 'GEO:'. $geo['lat'] .';'. $geo['long'] ."\n";

		// location
		if($this->getLocation() != '') $string .= 'LOCATION:'. SpoonIcal::formatAsString($this->getLocation()) ."\n";

		// organizer
		$organizer = $this->getOrganizer();
		if(!empty($organizer))
		{
			$string .= 'ORGANIZER';
			if(isset($organizer['cn'])) $string .= ';CN="'. $organizer['cn'] .'"';
			if(isset($organizer['dir'])) $string .= ';DIR="'. $organizer['dir'] .'"';
			if(isset($organizer['sentby'])) $string .= ';SENT-BY="'. $organizer['sentby'] .'"';
			if(isset($organizer['language'])) $string .= ';LANGUAGE="'. $organizer['language'] .'"';
			$string .= ':mailto:'. $organizer['email'] ."\n";
		}

		// priority
		if($this->getPriority() != '') $string .= 'PRIORITY:'. $this->getPriority() ."\n";

		// resources
		$resources = $this->getResources();
		if(!empty($resources)) $string .= 'RESOURCES:'. implode(',', $resources) ."\n";

		// sequence
		if($this->getSequence() != '') $string .= 'SEQUENCE:'. $this->getSequence() ."\n";

		// status
		if($this->getStatus() != '') $string .= 'STATUS:'. $this->getStatus() ."\n";

		// summary
		if($this->getSummary() != '') $string .= 'SUMMARY:'. SpoonIcal::formatAsString($this->getSummary()) ."\n";

		// time transparency
		if($this->getTimeTransparency() != '') $string .= 'TRANSP:'. $this->getTimeTransparency() ."\n";

		// unique identifier
		if($this->getUniqueIdentifier() != '') $string .= 'UID:'. $this->getUniqueIdentifier() ."\n";

		// url
		if($this->getUrl() != '') $string .= 'URL:'. $this->getUrl() ."\n";

		// xProperties
		$xProperties = $this->getXProperties();
		if(!empty($xProperties))
		{
			foreach($xProperties as $key => $value) $string .= $key .':'. $value ."\n";
		}

		// end string
		$string .= 'END:VEVENT';

		// return
		return $string;
	}


	/**
	 * Set the datetime end
	 *
	 * @return	void
	 * @param	int $timestamp		The end date and time.
	 */
	public function setDatetimeEnd($timestamp)
	{
		$this->datetimeEnd = (int) $timestamp;
	}


	/**
	 * Set the duration
	 *
	 * @return	void
	 * @param	string $duration	The positive duration.
	 */
	public function setDuration($duration)
	{
		$this->duration = (string) $duration;
	}


	/**
	 * Set GEO information
	 *
	 * @return	void
	 * @param	string $latitude		The latitude.
	 * @param	string $longitude		The longitude.
	 */
	public function setGeo($latitude, $longitude)
	{
		$this->geo = array('lat' => $latitude, 'long' => $longitude);
	}


	/**
	 * Set the location
	 *
	 * @return	void
	 * @param	string $location	The location.
	 */
	public function setLocation($location)
	{
		$this->location = (string) $location;
	}


	/**
	 * Set the priority
	 *
	 * @return	void
	 * @param	string $priority	The priority.
	 */
	public function setPriority($priority)
	{
		$this->priority = (string) $priority;
	}


	/**
	 * Set the resources
	 *
	 * @return	void
	 * @param	array $resources	An array containing the resources.
	 */
	public function setResources(array $resources)
	{
		$this->resources = $resources;
	}


	/**
	 * Set the status
	 *
	 * @return	void
	 * @param	string $status	The status of the event, possible values are: TENTATIVE, CONFIRMED, CANCELLED.
	 */
	public function setStatus($status)
	{
		// init var
		$possibleValues = array('TENTATIVE', 'CONFIRMED', 'CANCELLED');

		// redefine
		$status = (string) $status;

		// validate
		if(!in_array($status, $possibleValues)) throw new SpoonIcalException('Invalid status for event. Possible values are: '. implode(',', $possibleValues));

		// set
		$this->status = $status;
	}


	/**
	 * Set the time transparency
	 *
	 * @return	void
	 * @param	string $transparency	The transparency.
	 */
	public function setTimeTransparency($transparency)
	{
		$this->timeTransparency = (string) $transparency;
	}
}

?>