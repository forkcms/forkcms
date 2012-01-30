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
 * Creates an html time field
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormTime extends SpoonFormInput
{
	/**
	 * Class constructor.
	 *
	 * @param	string $name					The name.
	 * @param	string[optional] $value			The initial value.
	 * @param	string[optional] $class			The CSS-class to be used.
	 * @param	string[optional] $classError	The CSS-class to be used when there is an error.
	 */
	public function __construct($name, $value = null, $class = 'inputTimefield', $classError = 'inputTimefieldError')
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;

		/**
		 * If no value has presented, the current time
		 * will be used.
		 */
		if($value !== null) $this->setValue($value);
		else $this->setValue(date('H:i'));

		// custom optional fields
		$this->attributes['maxlength'] = 5;
		$this->attributes['class'] = (string) $class;
		$this->classError = (string) $classError;

		// update reserved attributes
		$this->reservedAttributes[] = 'maxlength';
	}


	/**
	 * Returns a timestamp based on the value & optional fields.
	 *
	 * @return	int
	 * @param	int[optional] $year		The year to use.
	 * @param	int[optional] $month	The month to use.
	 * @param	int[optional] $day		The day to use.
	 */
	public function getTimestamp($year = null, $month = null, $day = null)
	{
		// field has been filled in
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// valid field
			if($this->isValid())
			{
				// fetch time
				$hour = (int) substr($this->getValue(), 0, 2);
				$minute = (int) substr($this->getValue(), 3, 2);

				// init vars
				$year = ($year !== null) ? (int) $year : (int) date('Y');
				$month = ($month !== null) ? (int) $month : (int) date('n');
				$day = ($day !== null) ? (int) $day : (int) date('j');

				// create timestamp
				return mktime($hour, $minute, 0, $month, $day, $year);
			}
		}

		// nothing submitted
		return false;
	}


	/**
	 * Retrieve the initial or submitted value.
	 *
	 * @return	string
	 */
	public function getValue()
	{
		// redefine default value
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
	 * @param	string[optional] $error		The error message to set if the field isn't filled.
	 */
	public function isFilled($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!(isset($data[$this->attributes['name']]) && trim((string) $data[$this->attributes['name']]) != ''))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field is correctly submitted.
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set if the time isn't valid.
	 */
	public function isValid($error = null)
	{
		// field has been filled in
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);
			$data = (string) $data[$this->attributes['name']];

			// new time
			$time = '';

			// must be exactly 5 characters
			if(strlen($data) == 5)
			{
				// allowed characters
				$aCharacters = array(':', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

				// replace every character if it's not in the list!
				for($i = 0; $i < strlen($data); $i++)
				{
					if(in_array(substr($data, $i, 1), $aCharacters)) $time .= substr($data, $i, 1);
				}

				// maxlength checks out (needs to be equal)
				if(strlen($time) == 5 && strpos($time, ':') !== false)
				{
					// define hour & minutes
					$hour = (int) substr($time, 0, 2);
					$minutes = (int) substr($time, 3, 2);

					// validates
					if($hour >= 0 && $hour <= 23 && $minutes >= 0 && $minutes <= 59) return true;
				}
			}
		}

		/**
		 * If the script reaches this point, that means this field has not
		 * been successfully submitted, which results in returning a false
		 * and optionally defining an error.
		 */

		// error defined
		if($error !== null) $this->setError($error);

		// return status
		return false;
	}


	/**
	 * Parses the html for this time field.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template	The template to parse the element in.
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a time field. Please provide a name.');

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
	 * Set the value attribute for this time field.
	 *
	 * @return	SpoonFormTime
	 * @param	string $value	The new value for the element.
	 */
	public function setValue($value)
	{
		$this->value = (string) $value;
		return $this;
	}
}
