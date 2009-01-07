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
 * Creates an html time field
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonTimeField extends SpoonInputField
{
	/**
	 * Class constructor
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string $value
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, $value = null, $class = 'input-timefield', $classError = 'input-timefield-error')
	{
		// obligated fields
		$this->setId($name);
		$this->setName($name);

		/**
		 * If no value has presented, the current time
		 * will be used.
		 */
		if($value !== null) $this->setValue($value);
		else $this->setValue(date('H:i'));

		// custom optional fields
		if($class !== null) $this->setClass($class);
		if($classError !== null) $this->setClassOnError($classError);
	}


	/**
	 * Retrieve the initial or submitted value
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
			if(isset($data[$this->getName()]))
			{
				// value
				$value = $data[$this->getName()];
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

		// validate
		if(!(isset($data[$this->getName()]) && trim($data[$this->getName()]) != ''))
		{
			if($error !== null) $this->setError($error);
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

			// new time
			$time = '';

			// allowed characters
			$aCharacters = array(':', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

			// replace every character if it's not in the list!
			for($i = 0; $i < strlen($data[$this->getName()]); $i++)
			{
				if(in_array(substr($data[$this->getName()], $i, 1), $aCharacters)) $time .= substr($data[$this->getName()], $i, 1);
			}

			// maxlength checks out (needs to be equal)
			if(strlen($time) == 5)
			{
				// define hour & minutes
				$hour = substr($time, 0, 2);
				$minutes = substr($time, 3, 2);

				// validates
				if($hour >= 0 && $hour <= 23 && $minutes >= 0 && $minutes <= 59) return true;
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
	 * Parses the html for this time field
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->getName() == '') throw new SpoonFormException('A name is required for a time field. Please provide a name.');

		// start html generation
		$output = '<input type="text" id="'. $this->getId() .'" name="'. $this->getName() .'" value="'. $this->getValue() .'" maxlength="5"';

		// class / classOnError
		if($this->getClassAsHtml() != '') $output .= $this->getClassAsHtml();

		// style attribute
		if($this->style !== null) $output .= ' style="'. $this->getStyle() .'"';

		// tabindex
		if($this->tabindex !== null) $output .= ' tabindex="'. $this->getTabIndex() .'"';

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
	 * This method is useless, since the maxlength is limited to 5 charachters
	 *
	 * @return	void
	 */
	public function setMaxlength()
	{
		throw new SpoonException('This method is not to be used with the SpoonTimeField class. The maxlength setting is automatically generated.');
	}


	/**
	 * Set the value attribute for this time field
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