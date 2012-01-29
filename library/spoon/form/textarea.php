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
 * Create an html textarea
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormTextarea extends SpoonFormInput
{
	/**
	 * Is html allowed?
	 *
	 * @var	bool
	 */
	private $isHTML = false;


	/**
	 * Class constructor.
	 *
	 * @param	string $name					The name.
	 * @param	string[optional] $value			The initial value.
	 * @param	string[optional] $class			The CSS-class to be used.
	 * @param	string[optional] $classError	The CSS-class to be used when there is an error.
	 * @param	bool[optional] $HTML			Is HTML allowed?
	 */
	public function __construct($name, $value = null, $class = 'inputTextarea', $classError = 'inputTextareaError', $HTML = false)
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;

		// custom optional fields
		if($value !== null) $this->setValue($value);
		$this->attributes['cols'] = 62;
		$this->attributes['rows'] = 5;
		$this->attributes['class'] = $class;
		$this->classError = (string) $classError;
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
		$value = $this->value;

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
				$value = (string) $data[$this->attributes['name']];

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
		if(!(isset($data[$this->getName()]) && trim((string) $data[$this->attributes['name']]) != ''))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field's length is less than or equal to the given maximum.
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
	 * Checks if this field's length is more than or equal to the given minimum.
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
	 * Parses the html for this textarea.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template	The template to parse the element in.
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is requird for a textarea. Please provide a valid name.');

		// start html generation
		$output = '<textarea';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'], '[value]' => $this->getValue()));

		// close first tag
		$output .= '>';

		// add value
		$output .= str_replace(array('"', '<', '>'), array('&quot;', '&lt;', '&gt;'), $this->getValue());

		// end tag
		$output .= '</textarea>';

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
