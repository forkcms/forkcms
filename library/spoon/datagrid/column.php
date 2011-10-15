<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	datagrid
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */


/**
 * This class is internally used by the datagrid to hold the data
 * for every column.
 *
 * @package		spoon
 * @subpackage	datagrid
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		1.0.0
 */
class SpoonDatagridColumn
{
	/**
	 * Main cell attributes
	 *
	 * @var	array
	 */
	private $attributes = array('general' => array(), 'header' => array());


	/**
	 * Confirmation required (via URL)
	 *
	 * @var	bool
	 */
	private $confirm = false;


	/**
	 * Custom confirmation script
	 *
	 * @var	string
	 */
	private $confirmCustom;


	/**
	 * Confirmation message
	 *
	 * @var	string
	 */
	private $confirmMessage;


	/**
	 * Is this column hidden
	 *
	 * @var	bool
	 */
	private $hidden = false;


	/**
	 * Image for this column
	 *
	 * @var	string
	 */
	private $image;


	/**
	 * Alt/title tag for the image
	 *
	 * @var	string
	 */
	private $imageTitle;


	/**
	 * Label for the header column
	 *
	 * @var	string
	 */
	private $label;


	/**
	 * Name for this column
	 *
	 * @var	string
	 */
	private $name;


	/**
	 * Does the value overwrite images & url
	 *
	 * @var	bool
	 */
	private $overwriteValue = false;


	/**
	 * Sequence of this column
	 *
	 * @var	int
	 */
	private $sequence = 0;


	/**
	 * Is this column sortable
	 *
	 * @var	bool
	 */
	private $sorting = false;


	/**
	 * The default sorting method for this column
	 *
	 * @var	string
	 */
	private $sortingMethod = 'asc';


	/**
	 * URL for this column
	 *
	 * @var	string
	 */
	private $URL;


	/**
	 * Url title tag
	 *
	 * @var	string
	 */
	private $urlTitle;


	/**
	 * The value for this column
	 *
	 * @var	string
	 */
	private $value;


	/**
	 * Class constructor.
	 *
	 * @param	string $name				The name of the column.
	 * @param	string[optional] $label		The label to use for the column.
	 * @param	string[optional] $value		The value of the column.
	 * @param	string[optional] $URL		An URL for the column (the value will become a link).
	 * @param	string[optional] $title		The title for the URL.
	 * @param	string[optional] $image		An image instead of a value.
	 * @param	string[optional] $sequence	The sequence of the column.
	 */
	public function __construct($name, $label = null, $value = null, $URL = null, $title = null, $image = null, $sequence = null)
	{
		// name, label & value
		$this->name = (string) $name;
		$this->label = (string) $label;
		$this->value = (string) $value;

		// url, title & image
		if($URL !== null) $this->URL = (string) $URL;
		if($title !== null) $this->urlTitle = (string) $title;
		if($image !== null) $this->image = (string) $image;

		// sequence
		$this->sequence = (int) $sequence;
	}


	/**
	 * Clears the list of attributes for this column.
	 */
	public function clearAttributes()
	{
		$this->attributes['general'] = array();
	}


	/**
	 * Retrieve the attributes.
	 *
	 * @return	array
	 */
	public function getAttributes()
	{
		return $this->attributes['general'];
	}


	/**
	 * Retrieve the confirm setting.
	 *
	 * @return	bool
	 */
	public function getConfirm()
	{
		return $this->confirm;
	}


	/**
	 * Fetch the confirm custom script.
	 *
	 * @return	string
	 */
	public function getConfirmCustom()
	{
		return $this->confirmCustom;
	}


	/**
	 * Retrieve the confirm message.
	 *
	 * @return	string
	 */
	public function getConfirmMessage()
	{
		return $this->confirmMessage;
	}


	/**
	 * Retrieve the header attributes.
	 *
	 * @return	array
	 */
	public function getHeaderAttributes()
	{
		return $this->attributes['header'];
	}


	/**
	 * Retrieve the hidden status.
	 *
	 * @return	bool
	 */
	public function getHidden()
	{
		return $this->hidden;
	}


	/**
	 * Retrieve the image.
	 *
	 * @return	string
	 */
	public function getImage()
	{
		return $this->image;
	}


	/**
	 * Retrieve the image title.
	 *
	 * @return	string
	 */
	public function getImageTitle()
	{
		return $this->imageTitle;
	}


	/**
	 * Retrieve the label.
	 *
	 * @return	string
	 */
	public function getLabel()
	{
		return $this->label;
	}


	/**
	 * Retrieve the name.
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * Retrieve the overwrite value setting.
	 *
	 * @return	bool
	 */
	public function getOverwrite()
	{
		return $this->overwriteValue;
	}


	/**
	 * Retrieve the sequence.
	 *
	 * @return	int
	 */
	public function getSequence()
	{
		return $this->sequence;
	}


	/**
	 * Retrieve the sorting setting.
	 *
	 * @return	bool
	 */
	public function getSorting()
	{
		return $this->sorting;
	}


	/**
	 * Retrieve the default sorting method.
	 */
	public function getSortingMethod()
	{
		return $this->sortingMethod;
	}


	/**
	 * Retrieve the URL.
	 *
	 * @return	string
	 */
	public function getURL()
	{
		return $this->URL;
	}


	/**
	 * Retrieve the URL title tag.
	 *
	 * @return	string
	 */
	public function getURLTitle()
	{
		return $this->urlTitle;
	}


	/**
	 * Retrieve the value.
	 *
	 * @return	string
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * Set the attributes.
	 *
	 * @param	array $attributes	The attributes as key/value pairs.
	 */
	public function setAttributes(array $attributes)
	{
		foreach($attributes as $key => $value) $this->attributes['general'][(string) $key] = (string) $value;
	}


	/**
	 * Sets the confirm message.
	 *
	 * @param	string $message				The message to show.
	 * @param	string[optional] $custom	Custom code you wish to use to confirm.
	 */
	public function setConfirm($message, $custom = null)
	{
		$this->confirm = true;
		$this->confirmMessage = SpoonFilter::htmlentities((string) $message);
		$this->confirmCustom = (string) $custom;
	}


	/**
	 * Set the header attributes.
	 *
	 * @param	array $attributes	The attributes as key/value pairs.
	 */
	public function setHeaderAttributes(array $attributes)
	{
		foreach($attributes as $key => $value) $this->attributes['header'][(string) $key] = (string) $value;
	}


	/**
	 * Sets the hidden status.
	 *
	 * @param	bool[optional] $on	Should this column be hidden?
	 */
	public function setHidden($on = true)
	{
		$this->hidden = (bool) $on;
	}


	/**
	 * Sets the image.
	 *
	 * @param	string $image		The path to the image.
	 * @param	string $title		The title for the image.
	 */
	public function setImage($image, $title)
	{
		$this->image = (string) $image;
		$this->imageTitle = (string) $title;
	}


	/**
	 * Sets the label.
	 *
	 * @param	string $label	The label for the column.
	 */
	public function setLabel($label)
	{
		$this->label = (string) $label;
	}


	/**
	 * Sets the overwrite status.
	 *
	 * @param	bool[optional] $on	Should the current value be overwriten?
	 */
	public function setOverwrite($on = true)
	{
		$this->overwriteValue = (bool) $on;
	}


	/**
	 * Sets the sequence.
	 *
	 * @param	int $sequence	The new sequence of the column (0-based).
	 */
	public function setSequence($sequence)
	{
		$this->sequence = (int) $sequence;
	}


	/**
	 * Sets the sorting.
	 *
	 * @param	bool[optional] $on	Is this column sortable?
	 */
	public function setSorting($on = true)
	{
		$this->sorting = (bool) $on;
	}


	/**
	 * Sets the default sorting method for this column.
	 *
	 * @param	string[optional] $sort	The default sorting method, possible values are: asc, desc.
	 */
	public function setSortingMethod($sort = 'asc')
	{
		$this->sortingMethod = SpoonFilter::getValue($sort, array('asc', 'desc'), 'asc');
	}


	/**
	 * Sets the URL.
	 *
	 * @param	string $URL					The URL to set.
	 * @param	string[optional] $title		The title of the URL.
	 */
	public function setURL($URL, $title = null)
	{
		$this->URL = (string) $URL;
		$this->urlTitle = (string) $title;
	}


	/**
	 * Sets the value & its overwrite setting.
	 *
	 * @param	string $value				The new value.
	 * @param	bool[optional] $overwrite	Should the current value be overwritten?
	 */
	public function setValue($value, $overwrite = false)
	{
		$this->value = (string) $value;
		$this->overwriteValue = (bool) $overwrite;
	}
}
