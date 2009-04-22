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


/** SpoonFormVisualElement class */
require_once 'spoon/html/form/input_field.php';


/**
 * Create an html password field
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonPasswordField extends SpoonInputField
{
	/**
	 * Is the content of this field html?
	 *
	 * @var	bool
	 */
	private $isHTML = false;


	/**
	 * Class constructor
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	int[optional] $maxlength
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $HTML
	 */
	public function __construct($name, $value = null, $maxlength = null, $class = 'inputPassword', $classError = 'inputPasswordError', $HTML = false)
	{
		// obligated fields
		$this->setId(SpoonFilter::toCamelCase($name, '_', true));
		$this->setName($name);

		// custom optional fields
		if($value !== null) $this->setValue($value);
		if($maxlength !== null) $this->setMaxlength($maxlength);
		if($class !== null) $this->setClass($class);
		if($classError !== null) $this->setClassOnError($classError);
		$this->isHTML($HTML);
	}


	/**
	 * Retrieve the initial or submitted value
	 *
	 * @return	string
	 * @param	bool[optional] $allowHTML
	 */
	public function getValue($allowHTML = null)
	{
		// redefine html & default value
		$allowHTML = ($allowHTML !== null) ? (bool) $allowHTML : $this->isHTML;
		$value = ($this->isHTML) ? SpoonFilter::htmlentities($this->value) : $this->value;

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

				// maximum length?
				if($this->maxlength != '') substr($value, 0, $this->maxlength);

				// html allowed?
				if(!$allowHTML) $value = SpoonFilter::htmlentities($value);
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
	 * Make spoon aware that this field contains html
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function isHTML($on = true)
	{
		$this->isHTML = (bool) $on;
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
		if(!SpoonFilter::isValidAgainstRegexp($regexp, $data[$this->getName()]))
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
		if($this->getName() == '') throw new SpoonFormException('A name is required for a password field. Please provide a name.');

		// start html generation
		$output = '<input type="password" id="'. $this->id .'" name="'. $this->name .'" value="'. $this->getValue() .'"';

		// maximum number of characters
		if($this->maxlength) $output .= ' maxlength="'. $this->maxlength .'"';

		// class / classOnError
		if($this->getClassAsHTML() != '') $output .= $this->getClassAsHTML();

		// style attribute
		if($this->style !== null) $output .= ' style="'. $this->style .'"';

		// tabindex
		if($this->tabindex !== null) $output .= ' tabindex="'. $this->tabindex .'"';

		// readonly
		if($this->readOnly) $output .= ' readonly="readonly"';

		// add javascript methods
		if($this->getJavascriptAsHTML() != '') $output .= $this->getJavascriptAsHTML();

		// disabled
		if($this->disabled) $output .= ' disabled="disabled"';

		// end html
		$output .= ' />';

		// template
		if($template !== null)
		{
			$template->assign('txt'. SpoonFilter::toCamelCase($this->name), $output);
			$template->assign('txt'. SpoonFilter::toCamelCase($this->name) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
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