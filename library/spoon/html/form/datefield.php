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


/** SpoonFormInputField class */
require_once 'spoon/html/form/input_field.php';


/**
 * Creates an html textfield (date field)
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonDateField extends SpoonInputField
{
	/**
	 * Input mask (every item may only occur once)
	 *
	 * @var	string
	 */
	protected $mask = 'd-m-Y';


	/**
	 * Class constructor
	 *
	 * @return	void
	 * @param	string $name
	 * @param	int $value
	 * @param	string[optional] $mask
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, $value = null, $mask = null, $class = 'input-datefield', $classError = 'input-datefield-error')
	{
		// obligated fields
		$this->setId($name);
		$this->setName($name);

		/**
		 * The input mask defines the maxlength attribute, therefor
		 * this needs to be set anyhow. The mask needs to be updated
		 * before the value is set, or the old mask (in case it differs)
		 * will automatically be used.
		 */
		if($mask !== null) $this->setMask($mask);
		else $this->setMask($this->mask);

		/**
		 * The value will be filled based on the default input mask
		 * if no value has been defined.
		 */
		if($value !== null) $this->setValue($value);
		else $this->value = date(str_replace('y', 'Y', $this->mask));

		// custom optional fields
		if($class !== null) $this->setClass($class);
		if($classError !== null) $this->setClassOnError($classError);
	}


	/**
	 * Retrieve the initial value
	 *
	 * @return	string
	 */
	public function getDefaultValue()
	{
		return $this->value;
	}


	/**
	 * Retrieve the input mask
	 *
	 * @return	string
	 */
	public function getMask()
	{
		return $this->mask;
	}
	
	
	
	/**
	 * Returns a timestamp based on mask & optional fields
	 * 
	 * @return	int
	 * @param	int[optional] $year
	 * @param	int[optional] $month
	 * @param	int[optional] $day
	 * @param	int[optional] $hour
	 * @param	int[optional] $minute
	 * @param	int[optional] $second
	 */
	public function getTimestamp($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null)
	{
		// field has been filled in
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);
			
			// valid field
			if($this->isValid())
			{
				// define long mask
				$longMask = str_replace(array('d', 'm', 'y'), array('dd', 'mm', 'yyyy'), $this->mask);
				
				// year found
				if(strpos($longMask, 'yyyy') !== false) 
				{
					// redefine year
					$year = substr($data[$this->getName()], strpos($longMask, 'yyyy'), 4);
				}
				
				// month found
				if(strpos($longMask, 'mm') !== false)
				{
					// redefine month
					$month = substr($data[$this->getName()], strpos($longMask, 'mm'), 2);
				}
				
				// day found
				if(strpos($longMask, 'dd') !== false)
				{
					// redefine day
					$day = substr($data[$this->getName()], strpos($longMask, 'dd'), 2);
				}
			}
		}

		// create (default) time
		return mktime($hour, $minute, $second, $month, $day, $year);
	}


	/**
	 * Retrieve the initial or submitted value
	 *
	 * @return	string
	 * @param	bool[optional] $html
	 */
	public function getValue($allowHtml = false)
	{
		// redefine html & value
		$allowHtml = (bool) $allowHtml;
		$value = $this->value;

		// added to form
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);
			
			// submitted by post (may be empty)
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
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);
		
		// check filled status
		if(!(isset($data[$this->getName()]) && trim($data[$this->getName()]) != ''))
		{
			if($error !== null) $this->addError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field is correctly submitted
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isValid($error = null)
	{
		// field has been filled in
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);
			
			// maxlength checks out (needs to be equal)
			if(strlen($data[$this->getName()]) == $this->maxlength)
			{
				// define long mask
				$longMask = str_replace(array('d', 'm', 'y'), array('dd', 'mm', 'yyyy'), $this->mask);

				// validate year (yyyy)
				if(strpos($longMask, 'yyyy') !== false)
				{
					// redefine year
					$year = substr($data[$this->getName()], strpos($longMask, 'yyyy'), 4);
					
					// not an int
					if(!SpoonFilter::isInteger($year)) { $this->addError($error); return false; }

					// invalid year
					if(!checkdate(1, 1, $year)) { $this->addError($error); return false; }
				}

				// validate month (mm)
				if(strpos($longMask, 'mm') !== false)
				{
					// redefine month
					$month = substr($data[$this->getName()], strpos($longMask, 'mm'), 2);
					
					// not an int
					if(!SpoonFilter::isInteger($month)) { $this->addError($error); return false; }

					// invalid month
					if(!checkdate($month, 1, $year)) { $this->setError($error); return false; }
				}

				// validate day (dd)
				if(strpos($longMask, 'dd') !== false)
				{
					// redefine day
					$day = substr($data[$this->getName()], strpos($longMask, 'dd'), 2);
					
					// not an int
					if(!SpoonFilter::isInteger($day)) { $this->addError($error); return false; }

					// invalid day
					if(!checkdate($month, $day, $year)) { $this->setError($error); return false; }
				}
			}

			// maximum length doesn't check out
			else { $this->addError($error); return false; }
		}

		// not filled out
		else { $this->addError($error); return false; }

		/**
		 * When the code reaches the point, it means no errors have occured
		 * and the true status may be returned.
		 */
		return true;
	}


	/**
	 * Parses the html for this date field
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// not yet parsed
		if(!$this->parsed)
		{
			// name is required
			if($this->getName() == '') throw new SpoonFormException('A name is required for a date field. Please provide a valid name.');

			// start html generation
			$this->html = '<input type="text" id="' . $this->getId() . '" name="' . $this->getName() .'" value="'. $this->getValue() .'" maxlength="'. $this->maxlength .'"';

			// class / classOnError
			if($this->getClassAsHtml() != '') $this->html .= $this->getClassAsHtml();

			// style attribute
			if($this->style !== null) $this->html .= ' style="'. $this->getStyle() .'"';

			// tabindex
			if($this->tabindex !== null) $this->html .= ' tabindex="'. $this->getTabIndex() .'"';

			// readonly
			if($this->readOnly) $this->html .= ' readonly="readonly"';

			// add javascript methods
			if($this->getJavascriptAsHtml() != '') $this->html .= $this->getJavascriptAsHtml();

			// disabled
			if($this->disabled) $this->html .= ' disabled="disabled"';

			// end html
			$this->html .= ' />';

			// parsed status
			$this->parsed = true;
		}
	}


	/**
	 * Set the input mask
	 *
	 * @return	void
	 * @param	string[optional] $mask
	 */
	public function setMask($mask = null)
	{
		// redefine mask
		$mask = ($mask !== null) ? strtolower((string) $mask) : $this->mask;

		// allowed characters
		$aCharachters = array('.', '-', '/', 'd', 'm', 'y');

		// new mask
		$maskCorrected = '';

		// loop all elements
		for($i = 0; $i < strlen($mask); $i++)
		{
			// char allowed
			if(in_array(substr($mask, $i, 1), $aCharachters)) $maskCorrected .= substr($mask, $i, 1);
		}

		// new mask
		$this->mask = $maskCorrected;

		// define maximum length for this element
		$maskCorrected = str_replace(array('d', 'm', 'y'), array('dd', 'mm', 'yyyy'), $maskCorrected);

		// update maxium length
		$this->maxlength = strlen($maskCorrected);
	}


	/**
	 * This method is overwritten by the setMask, and therefor useless
	 *
	 * @return	void
	 */
	public function setMaxlength()
	{
		throw new SpoonFormException('This method is not to be used with the SpoonDateField class. The maxlength is generated automatically based on the mask.');
	}


	/**
	 * Set the value attribute for this date field
	 *
	 * @return	void
	 * @param	int $value
	 */
	public function setValue($value)
	{
		$this->value = date($this->mask, (int) $value);
	}
}

?>