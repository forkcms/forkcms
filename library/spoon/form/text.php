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
 * Create a form textfield.
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormText extends SpoonFormInput
{
	/**
	 * Is the content of this field html?
	 *
	 * @var	bool
	 */
	private $isHTML = false;


	/**
	 * Overrule the reserverd attributes
	 *
	 * @var array
	 */
	protected $reservedAttributes = array('name', 'value');


	/**
	 * Class constructor.
	 *
	 * @param	string $name					The name.
	 * @param	string[optional] $value			The initial value.
	 * @param	int[optional] $maxlength		The maximum-length the value can be.
	 * @param	string[optional] $class			The CSS-class to be used.
	 * @param	string[optional] $classError	The CSS-class to be used when there is an error.
	 * @param	bool[optional] $HTML			Is HTML allowed?
	 */
	public function __construct($name, $value = null, $maxlength = null, $class = 'inputText', $classError = 'inputTextError', $HTML = false)
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;

		// custom optional fields
		if($value !== null) $this->value = (string) $value;
		if($maxlength !== null) $this->attributes['maxlength'] = (int) $maxlength;
		$this->attributes['type'] = 'text';
		$this->attributes['class'] = (string) $class;
		if($classError !== null) $this->classError = (string) $classError;
		$this->isHTML = (bool) $HTML;
	}


	/**
	 * Retrieve the initial or submitted value.
	 *
	 * @return	string
	 * @param	bool[optional] $allowHTML	Is HTML allowed?
	 */
	public function getValue($allowHTML = null)
	{
		// redefine html & default value
		$allowHTML = ($allowHTML !== null) ? (bool) $allowHTML : $this->isHTML;
		$value = (string) $this->value;

		// contains html
		if($this->isHTML)
		{
			// set value
			$value = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($value) : SpoonFilter::htmlentities($value);
		}

		// form submitted
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// submitted by post (may be empty)
			if(isset($data[$this->getName()]))
			{
				// value
				$value = $data[$this->attributes['name']];

				// maximum length?
				if(isset($this->attributes['maxlength']) && $this->attributes['maxlength'] > 0) $value = mb_substr($value, 0, (int) $this->attributes['maxlength'], SPOON_CHARSET);

				// html allowed?
				if(!$allowHTML) $value = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($value) : SpoonFilter::htmlentities($value);
			}
		}

		return $value;
	}


	/**
	 * Checks if this field contains only letters a-z and A-Z.
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isAlphabetical($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isAlphabetical($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field only contains letters & numbers (without spaces).
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isAlphaNumeric($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isAlphaNumeric($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if the field is between a given minimum and maximum (includes min & max).
	 *
	 * @return	bool
	 * @param	int $minimum				The minimum.
	 * @param	int $maximum				The maximum.
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isBetween($minimum, $maximum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isBetween($minimum, $maximum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks this field for a boolean (true/false | 0/1).
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isBool($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isBool($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field only contains numbers 0-9.
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isDigital($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isDigital($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks this field for a valid e-mail address.
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isEmail($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isEmail($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// has error
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks for a valid file name (including dots but no slashes and other forbidden characters).
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isFilename($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isFilename($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// has error
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field was submitted & filled.
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
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
	 * Checks this field for numbers 0-9 and an optional - (minus) sign (in the beginning only).
	 *
	 * @return	bool
	 * @param	string[optional] $error			The error message to set.
	 * @param	bool[optional] $allowCommas		Do you want to use commas as a decimal separator?
	 */
	public function isFloat($error = null, $allowCommas = false)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isFloat($data[$this->attributes['name']], $allowCommas))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field is greater than another value.
	 *
	 * @return	bool
	 * @param	int $minimum				The minimum.
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isGreaterThan($minimum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isGreaterThan($minimum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks this field for numbers 0-9 and an optional - (minus) sign (in the beginning only).
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isInteger($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isInteger($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field is a proper ip address.
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isIp($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isIp($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field does not exceed the given maximum.
	 *
	 * @return	bool
	 * @param	int $maximum				The maximum.
	 * @param	int[optional] $error		The error message to set.
	 */
	public function isMaximum($maximum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMaximum($maximum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field's length is less (or equal) than the given maximum.
	 *
	 * @return	bool
	 * @param	int $maximum				The maximum number of characters.
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isMaximumCharacters($maximum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMaximumCharacters($maximum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field is at least a given minimum.
	 *
	 * @return	bool
	 * @param	int $minimum				The minimum.
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isMinimum($minimum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMinimum($minimum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field's length is more (or equal) than the given minimum.
	 *
	 * @return	bool
	 * @param	int $minimum				The minimum number of characters.
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isMinimumCharacters($minimum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMinimumCharacters($minimum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Alias for isDigital (Field may only contain numbers 0-9).
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isNumeric($error = null)
	{
		return $this->isDigital($error);
	}


	/**
	 * Checks this field for numbers 0-9 and an optional - (minus) sign (in the beginning only).
	 *
	 * @return	bool
	 * @param	string[optional] $error			The error message to set.
	 * @param	int[optional] $precision		The allowed number of digits after the decimal separator. Defaults to 2.
	 * @param	bool[optional] $allowNegative	Do you want to allow negative prices? Defaults to false.
	 * @param	bool[optional] $allowCommas		Do you want to use commas as a decimal separator? Defaults to true.
	 */
	public function isPrice($error = null, $precision = 2, $allowNegative = false, $allowCommas = true)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isPrice($data[$this->attributes['name']], $allowCommas))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if the field is smaller than a given maximum.
	 *
	 * @return	bool
	 * @param	int $maximum				The maximum.
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isSmallerThan($maximum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isSmallerThan($maximum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field contains any string that doesn't have control characters (ASCII 0 - 31) but spaces are allowed.
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isString($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isString($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks this field for a valid url.
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isURL($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isURL($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if the field validates against the regexp.
	 *
	 * @return	bool
	 * @param	string $regexp				The regular expresion to test the value.
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isValidAgainstRegexp($regexp, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isValidAgainstRegexp((string) $regexp, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Parses the html for this textfield.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template	The template to parse the element in.
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a textfield. Please provide a name.');

		// start html generation
		$output = '<input value="' . str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $this->getValue()) . '"';

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
}
