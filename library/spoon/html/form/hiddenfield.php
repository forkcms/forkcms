<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @author			Tijs Verkoyen <tijs@spoon-library.be>
 * @since			0.1.1
 */


/** SpoonFormElement class */
require_once 'spoon/html/form/element.php';


/**
 * Creates an html hidden field
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			1.0.0
 */
class SpoonHiddenField extends SpoonFormElement 
{
	/**
	 * Is the content of this field html?
	 * 
	 * @var	bool
	 */
	private $isHtml = false;
	
	
	/**
	 * Value of this hidden field
	 * 
	 * @var	string
	 */
	private $value;
	
	
	/**
	 * Class constructor.
	 * 
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 */
	public function __construct($name, $value = null)
	{
		// obligated fields
		$this->setName($name);
		
		// value
		if($value !== null) $this->setValue($value);
	}
	
	
	/**
	 * Retrieve the initial or submitted value
	 *
	 * @return	string
	 * @param	bool[optional] $html
	 */
	public function getValue($allowHtml = false)
	{
		// redefine html & default value
		$allowHtml = (bool) $allowHtml;
		$value = ($this->isHtml) ? SpoonFilter::htmlentities($this->value) : $this->value;

		// added to form
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);
			
			// submitted by post/get (may be empty)
			if(isset($data[$this->getName()]))
			{
				// value
				$value = $data[$this->getName()];

				// html allowed?
				if(!$allowHtml) $value = SpoonFilter::htmlentities($value);
			}
		}

		return $value;
	}


	/**
	 * Checks if this field has any content (except spaces)
	 *
	 * @return	bool
	 */
	public function isFilled()
	{
		// post/get data
		$data = $this->getMethod(true);
		
		// validate
		if(!(isset($data[$this->getName()]) && trim($data[$this->getName()]) != '')) return false;
		return true;
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $on
	 */
	public function isHtml($on = true)
	{
		$this->isHtml = (bool) $on;
	}


	/**
	 * Parses the html for this hidden field
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// not yet parsed
		if(!$this->parsed)
		{
			// name is required
			if($this->name == '') throw new SpoonFormException('A name is required for a hidden field. Please provide a name.');

			// start html generation
			$this->html = '<input type="hidden" ';
			
			// add id?
			if($this->id !== null) $this->html .= 'id="'. $this->id .'"';
			
			// add other elements
			$this->html .= ' name="'. $this->name .'" value="'. $this->getValue() .'" />';

			// parsed status
			$this->parsed = true;
		}
	}
	
	
	/**
	 * Set the value attribute for this hidden field
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setValue($value)
	{
		$this->value = (string) $value;
	}
}

?>