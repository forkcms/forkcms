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
 * Create a form password element.
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormPassword extends SpoonFormInput
{
	/**
	 * Class constructor.
	 *
	 * @param	string $name					The name.
	 * @param	string[optional] $value			The initial value.
	 * @param	int[optional] $maxlength		The maximum-length the value can be.
	 * @param	string[optional] $class			The CSS-class to be used.
	 * @param	string[optional] $classError	The CSS-class to be used when there is an error.
	 */
	public function __construct($name, $value = null, $maxlength = null, $class = 'inputPassword', $classError = 'inputPasswordError')
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;

		// custom optional fields
		if($value !== null) $this->setValue($value);
		if($maxlength !== null) $this->attributes['maxlength'] = (int) $maxlength;
		$this->attributes['class'] = (string) $class;
		$this->classError = (string) $classError;
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

		// form submitted
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// submitted by post (may be empty)
			if(isset($data[$this->getName()]))
			{
				// value
				$value = (string) $data[$this->attributes['name']];

				// maximum length?
				if(isset($this->attributes['maxlength']) && $this->attributes['maxlength'] > 0) $value = mb_substr($value, 0, (int) $this->attributes['maxlength'], SPOON_CHARSET);
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
	 * Checks if the field validates against the regexp.
	 *
	 * @return	bool
	 * @param	string $regexp				The regexp to test the value on.
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
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isValidAgainstRegexp($regexp, $data[$this->attributes['name']]))
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
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a password field. Please provide a name.');

		// start html generation
		$output = '<input type="password" value="' . str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $this->getValue()) . '"';

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
	 * Set the initial value.
	 *
	 * @param	string $value	The new value.
	 */
	private function setValue($value)
	{
		$this->value = (string) $value;
	}
}
