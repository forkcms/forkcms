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
 * The base class for every text input field
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormInput extends SpoonFormAttributes
{
	/**
	 * Class attribute on error
	 *
	 * @var	string
	 */
	protected $classError;


	/**
	 * Errors stack
	 *
	 * @var	string
	 */
	protected $errors;


	/**
	 * Initial value
	 *
	 * @var	string
	 */
	protected $value;


	/**
	 * Adds an error to the error stack.
	 *
	 * @param	string $error	The errormessage to set.
	 */
	public function addError($error)
	{
		$this->errors .= (string) $error;
	}


	/**
	 * Retrieve the class html.
	 *
	 * @return	string
	 */
	protected function getClassHTML()
	{
		// default value
		$value = '';

		// has errors
		if($this->errors != '')
		{
			// class & classOnError defined
			if($this->attributes['class'] != '' && $this->classError != '') $value = ' class="' . $this->attributes['class'] . ' ' . $this->classError . '"';

			// only class defined
			elseif($this->attributes['class'] != '') $value = ' class="' . $this->attributes['class'] . '"';

			// only error defined
			elseif($this->classError != '') $value = ' class="' . $this->classError . '"';
		}

		// no errors
		else
		{
			// class defined
			if($this->attributes['class'] != '') $value = ' class="' . $this->attributes['class'] . '"';
		}

		return $value;
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
	 * Retrieve the errors.
	 *
	 * @return	string
	 */
	public function getErrors()
	{
		return $this->errors;
	}


	/**
	 * Overwrites the entire error stack.
	 *
	 * @return	SpoonFormInput
	 * @param	string $error	The errormessage to set.
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
		return $this;
	}
}
