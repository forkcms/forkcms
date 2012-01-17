<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */


/**
 * Creates an html textfield (date field)
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @since		0.1.1
 */
class SpoonFormDate extends SpoonFormInput
{
	/**
	 * Input mask (every item may only occur once)
	 *
	 * @var	string
	 */
	protected $mask = 'd-m-Y';


	/**
	 * The value needed to base the mask on
	 *
	 * @var	int
	 */
	private $defaultValue;


	/**
	 * Class constructor.
	 *
	 * @param	string $name					The name.
	 * @param	mixed[optional] $value			The initial value.
	 * @param	string[optional] $mask			The mask to use.
	 * @param	string[optional] $class			The CSS-class to be used.
	 * @param	string[optional] $classError	The CSS-class to be used when there is an error.
	 */
	public function __construct($name, $value = null, $mask = null, $class = 'inputDatefield', $classError = 'inputDatefieldError')
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;

		/**
		 * The input mask defines the maxlength attribute, therefor
		 * this needs to be set anyhow. The mask needs to be updated
		 * before the value is set, or the old mask (in case it differs)
		 * will automatically be used.
		 */
		$this->setMask(($mask !== null) ? $mask : $this->mask);

		/**
		 * The value will be filled based on the default input mask
		 * if no value has been defined.
		 */
		$this->defaultValue = ($value !== null) ? (($value !== '') ? (int) $value : '') : time();
		$this->setValue($this->defaultValue);

		// custom optional fields
		$this->attributes['class'] = (string) $class;
		$this->classError = (string) $classError;

		// update reserved attributes
		$this->reservedAttributes[] = 'maxlength';
	}


	/**
	 * Retrieve the initial value.
	 *
	 * @return	string
	 */
	public function getDefaultValue()
	{
		return $this->value;
	}


	/**
	 * Retrieve the input mask.
	 *
	 * @return	string
	 */
	public function getMask()
	{
		return $this->mask;
	}


	/**
	 * Returns a timestamp based on mask & optional fields.
	 *
	 * @return	int
	 * @param	int[optional] $year		The year to use.
	 * @param	int[optional] $month	The month to use.
	 * @param	int[optional] $day		The day to use.
	 * @param	int[optional] $hour		The hour to use.
	 * @param	int[optional] $minute	The minutes to use.
	 * @param	int[optional] $second	The seconds to use.
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
				$longMask = str_replace(array('d', 'm', 'Y'), array('dd', 'mm', 'yyyy'), $this->mask);

				// year found
				if(strpos($longMask, 'yyyy') !== false && $year === null)
				{
					// redefine year
					$year = substr($data[$this->attributes['name']], strpos($longMask, 'yyyy'), 4);
				}

				// month found
				if(strpos($longMask, 'mm') !== false && $month === null)
				{
					// redefine month
					$month = substr($data[$this->attributes['name']], strpos($longMask, 'mm'), 2);
				}

				// day found
				if(strpos($longMask, 'dd') !== false && $day === null)
				{
					// redefine day
					$day = substr($data[$this->attributes['name']], strpos($longMask, 'dd'), 2);
				}
			}

			// init vars
			$year = ($year !== null) ? (int) $year : (int) date('Y');
			$month = ($month !== null) ? (int) $month : (int) date('n');
			$day = ($day !== null) ? (int) $day : (int) date('j');
			$hour = ($hour !== null) ? (int) $hour : (int) date('H');
			$minute = ($minute !== null) ? (int) $minute : (int) date('i');
			$second = ($second !== null) ? (int) $second : (int) date('s');
		}

		// create (default) time
		return mktime($hour, $minute, $second, $month, $day, $year);
	}


	/**
	 * Retrieve the initial or submitted value.
	 *
	 * @return	string
	 */
	public function getValue()
	{
		// redefine html & value
		$value = $this->value;

		// added to form
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// submitted by post (may be empty)
			if(isset($data[$this->attributes['name']]))
			{
				// value
				$value = (string) $data[$this->attributes['name']];
			}
		}

		return $value;
	}


	/**
	 * Checks if this field has any content (except spaces).
	 *
	 * @return	bool
	 * @param	string[optional] $error		The errormessage to set.
	 */
	public function isFilled($error = null)
	{
		// form submitted
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// check filled status
			if(!(isset($data[$this->getName()]) && trim((string) $data[$this->getName()]) != ''))
			{
				if($error !== null) $this->setError($error);
				return false;
			}
		}

		return true;
	}


	/**
	 * Checks if this field is correctly submitted.
	 *
	 * @return	bool
	 * @param	string[optional] $error		The errormessage to set.
	 */
	public function isValid($error = null)
	{
		// field has been filled in
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// maxlength checks out (needs to be equal)
			if(strlen((string) $data[$this->attributes['name']]) == $this->attributes['maxlength'])
			{
				// define long mask
				$longMask = str_replace(array('d', 'm', 'y', 'Y'), array('dd', 'mm', 'yy', 'yyyy'), $this->mask);

				// init vars
				$year = (int) date('Y');
				$month = (int) date('m');
				$day = (int) date('d');

				// validate year (yyyy)
				if(strpos($longMask, 'yyyy') !== false)
				{
					// redefine year
					$year = substr($data[$this->attributes['name']], strpos($longMask, 'yyyy'), 4);

					// not an int
					if(!SpoonFilter::isInteger($year))
					{
						if($error !== null) $this->setError($error);
						return false;
					}

					// invalid year
					if(!checkdate(1, 1, $year))
					{
						if($error !== null) $this->setError($error);
						return false;
					}
				}

				// validate year (yy)
				if(strpos($longMask, 'yy') !== false && strpos($longMask, 'yyyy') === false)
				{
					// redefine year
					$year = substr($data[$this->attributes['name']], strpos($longMask, 'yy'), 2);

					// not an int
					if(!SpoonFilter::isInteger($year))
					{
						if($error !== null) $this->setError($error);
						return false;
					}

					// invalid year
					if(!checkdate(1, 1, '19' . $year))
					{
						if($error !== null) $this->setError($error);
						return false;
					}
				}

				// validate month (mm)
				if(strpos($longMask, 'mm') !== false)
				{
					// redefine month
					$month = substr($data[$this->attributes['name']], strpos($longMask, 'mm'), 2);

					// not an int
					if(!SpoonFilter::isInteger($month))
					{
						if($error !== null) $this->setError($error);
						return false;
					}

					// invalid month
					if(!checkdate($month, 1, $year))
					{
						if($error !== null) $this->setError($error);
						return false;
					}
				}

				// validate day (dd)
				if(strpos($longMask, 'dd') !== false)
				{
					// redefine day
					$day = substr($data[$this->attributes['name']], strpos($longMask, 'dd'), 2);

					// not an int
					if(!SpoonFilter::isInteger($day))
					{
						if($error !== null) $this->setError($error);
						return false;
					}

					// invalid day
					if(!checkdate($month, $day, $year))
					{
						if($error !== null) $this->setError($error);
						return false;
					}
				}
			}

			// maximum length doesn't check out
			else
			{
				if($error !== null) $this->setError($error);
				return false;
			}
		}

		// not filled out
		else
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		/**
		 * When the code reaches the point, it means no errors have occured
		 * and truth will out!
		 */
		return true;
	}


	/**
	 * Parses the html for this date field.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template	The template to parse the element in.
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a date field. Please provide a valid name.');

		// start html generation
		$output = '<input type="text" value="' . $this->getValue() . '"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'], '[value]' => $this->getValue())) . ' />';

		// template
		if($template !== null)
		{
			$template->assign('txt' . SpoonFilter::toCamelCase($this->attributes['name']), $output);
			$template->assign('txt' . SpoonFilter::toCamelCase($this->attributes['name']) . 'Error', ($this->errors != '') ? '<span class="formError">' . $this->errors . '</span>' : '');
		}

		return $output;
	}


	/**
	 * Set the input mask.
	 *
	 * @return	SpoonFormDate
	 * @param	string[optional] $mask	The date-mask.
	 */
	public function setMask($mask = null)
	{
		// redefine mask
		$mask = ($mask !== null) ? (string) $mask : $this->mask;

		// allowed characters
		$aCharachters = array('.', '-', '/', 'd', 'm', 'y', 'Y');

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
		$maskCorrected = str_replace(array('d', 'm', 'y', 'Y'), array('dd', 'mm', 'yy', 'yyyy'), $maskCorrected);

		// update maxium length
		$this->attributes['maxlength'] = strlen($maskCorrected);

		// update value
		if($this->defaultValue !== null) $this->setValue($this->defaultValue);
		return $this;
	}


	/**
	 * Set the value attribute for this date field.
	 *
	 * @param	mixed $value		The new value.
	 */
	private function setValue($value)
	{
		$this->value = ($value === '' ? '' : date($this->mask, (int) $value));
	}
}
