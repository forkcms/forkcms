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


/** SpoonInputField class */
require_once 'spoon/html/form/input_field.php';


/**
 * Create an html textfield
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonTextField extends SpoonInputField
{
	/**
	 * Is the content of this field html?
	 *
	 * @var	bool
	 */
	private $isHtml = false;


	/**
	 * Class constructor
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	int[optional] $maxlength
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $html
	 */
	public function __construct($name, $value = null, $maxlength = null, $class = 'input-text', $classError = 'input-text-error', $html = false)
	{
		// obligated fields
		$this->setId($name);
		$this->setName($name);

		// custom optional fields
		if($value !== null) $this->setValue($value);
		if($maxlength !== null) $this->setMaxlength($maxlength);
		if($class !== null) $this->setClass($class);
		if($classError !== null) $this->setClassOnError($classError);
		$this->isHtml($html);
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
		$allowHtml = ($allowHtml !== null) ? (bool) $allowHtml : $this->isHtml;
		$value = ($this->isHtml) ? SpoonFilter::htmlentities($this->value) : $this->value;

		// form submitted
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
	 * Checks if this field contains only letters a-z and A-Z
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isAlphabetical($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isAlphabetical($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field only contains letters & numbers (without spaces)
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isAlphaNumeric($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isAlphaNumeric($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if the field is between a given minimum and maximum (includes min & max)
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	int $maximum
	 * @param	string[optional] $error
	 */
	public function isBetween($minimum, $maximum, $error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isBetween($minimum, $maximum, $data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks this field for a boolean (true/false | 0/1)
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isBool($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isBool($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field only contains numbers 0-9
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isDigital($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isDigital($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks this field for a valid e-mail address
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isEmail($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isEmail($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks for a valid file name (including dots but no slashes and other forbidden characters)
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilename($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isFilename($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field was submitted & filled
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!(isset($data[$this->getName()]) && trim($data[$this->getName()]) != ''))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks this field for numbers 0-9 and an optional - (minus) sign (in the beginning only)
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFloat($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isFloat($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field is greater than another value
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	string[optional] $error
	 */
	public function isGreaterThan($minimum, $error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isGreaterThan($minimum, $data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Make spoon aware that this field contains html
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function isHtml($on = true)
	{
		$this->isHtml = (bool) $on;
	}


	/**
	 * Checks this field for numbers 0-9 and an optional - (minus) sign (in the beginning only)
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isInteger($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isInteger($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field is a proper ip address
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isIp($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isIp($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field does not exceed the given maximum
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param	int[optional] $error
	 */
	public function isMaximum($maximum, $error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isMaximum($maximum, $data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field's length is less (or equal) than the given maximum
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param	string[optional] $error
	 */
	public function isMaximumCharacters($maximum, $error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isMaximumCharacters($maximum, $data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field is at least a given minimum
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	string[optional] $error
	 */
	public function isMinimum($minimum, $error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isMinimum($minimum, $data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field's length is more (or equal) than the given minimum
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	string[optional] $error
	 */
	public function isMinimumCharacters($minimum, $error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isMinimumCharacters($minimum, $data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Alias for isDigital (Field may only contain numbers 0-9)
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isNumeric($error = null)
	{
		return $this->isDigital($error);
	}


	/**
	 * Checks if the field is smaller than a given maximum
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param	string[optional] $error
	 */
	public function isSmallerThan($maximum, $error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isSmallerThan($maximum, $data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field contains any string that doesn't have control characters (ASCII 0 - 31) but spaces are allowed
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isString($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isString($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks this field for a valid url
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isURL($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isURL($data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if the field validates against the regexp
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isValidAgainstRegexp($regexp, $error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!SpoonFilter::isValidAgainstRegexp((string) $regexp, $data[$this->getName()]))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Parses the html for this textfield
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->getName() == '') throw new SpoonFormException('A name is required for a textfield. Please provide a name.');

		// start html generation
		$output = '<input type="text" id="'. $this->id .'" name="'. $this->name .'" value="'. $this->getValue() .'"';

		// maximum number of characters
		if($this->maxlength !== null) $output .= ' maxlength="'. $this->maxlength .'"';

		// class / classOnError
		if($this->getClassAsHtml() != '') $output .= $this->getClassAsHtml();

		// style attribute
		if($this->style !== null) $output .= ' style="'. $this->style .'"';

		// tabindex
		if($this->tabindex !== null) $output .= ' tabindex="'. $this->tabindex .'"';

		// readonly
		if($this->readOnly) $output .= ' readonly="readonly"';

		// add javascript methods
		if($this->getJavascriptAsHtml() != '') $output .= $this->getJavascriptAsHtml();

		// disabled
		if($this->disabled) $output .= ' disabled="disabled"';

		// end html
		$output .= ' />';

		// template
		if($template !== null)
		{
			$template->assign('txt'. SpoonFilter::toCamelCase($this->name), $output);
			$template->assign('txt'. SpoonFilter::toCamelCase($this->name) .'Error', ($this->errors!= '') ? '<span class="form-error">'. $this->errors .'</span>' : '');
		}

		// cough
		return $output;
	}


	/**
	 * Set the initial value
	 *
	 * @return	void
	 * @param	string $value
	 */
	private function setValue($value)
	{
		$this->value = (string) $value;
	}
}

?>