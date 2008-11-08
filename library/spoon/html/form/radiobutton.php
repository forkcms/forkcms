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


/** SpoonVisualElement class */
require_once 'spoon/html/form/visual_element.php';


/**
 * Creates an html radiobutton
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonRadioButton extends SpoonVisualFormElement
{
	/**
	 * Currently checked or not
	 *
	 * @var	bool
	 */
	private $checked = false;


	/**
	 * Class attribute on error
	 *
	 * @var	string
	 */
	private $classError;


	/**
	 * Errors stack
	 *
	 * @var	string
	 */
	private $errors;


	/**
	 * Html value attribute
	 *
	 * @var	string
	 */
	protected $value;


	/**
	 * Class constructor
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string $value
	 * @param	bool[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, $value, $checked = false, $class = 'input-radiobutton', $classError = 'input-radiobutton-error')
	{
		// obligated fields
		$this->setId($name .'_'. $value);
		$this->setName($name);
		$this->setValue($value);

		// custom optional fields
		if($checked) $this->setChecked($checked);
		if($class !== null) $this->setClass($class);
		if($classError !== null) $this->setClassOnError($classError);
	}


	/**
	 * Adds an error to the error stack
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function addError($error)
	{
		$this->errors .= (string) $error;
	}


	/**
	 * Retrieve the checked status
	 *
	 * @return	bool
	 */
	public function getChecked()
	{
		/**
		 * If we want to retrieve the checked status, we should first
		 * ensure that the value we return is correct, therefor we
		 * check the $_POST/$_GET array for the right value & ajust it if needed.
		 */
		
		// post/get data
		$data = $this->getMethod(true);

		// form submitted
		if($this->isSubmitted())
		{
			// currently field checked
			if(isset($data[$this->getName()]) && $data[$this->getName()] == $this->value) $this->setChecked(true);

			// current field NOT checked
			else $this->setChecked(false);
		}

		return $this->checked;
	}


	/**
	 * Retrieves the class based on the errors status
	 *
	 * @return	string
	 */
	public function getClassAsHtml()
	{
		// default value
		$value = '';
		
		// has errors
		if($this->errors != '')
		{
			// class & classOnError defined
			if($this->class != '' && $this->classError != '') $value = ' class="'. $this->class .' '. $this->classError .'"';
			
			// only class defined
			elseif($this->class != '') $value = ' class="'. $this->class .'"';
			
			// only error defined
			elseif($this->classError != '') $value = ' class="'. $this->classError .'"';
		}
		
		// no errors
		else 
		{
			// class defined
			if($this->class != '') $value = ' class="'. $this->class .'"';
		}
		
		return $value;
	}


	/**
	 * Retrieve the class on error
	 *
	 * @return	string
	 */
	public function getClassOnError()
	{
		return $this->classError;
	}


	/**
	 * Retrieves the initial value
	 *
	 * @return	string
	 */
	public function getDefaultValue()
	{
		return $this->value;
	}


	/**
	 * Retrieve the errors
	 *
	 * @return	string
	 */
	public function getErrors()
	{
		return $this->errors;
	}


	/**
	 * Retrieves the initial or submitted value
	 *
	 * @return	string
	 * @param	bool[optional] $html
	 */
	public function getValue($allowHtml = false)
	{
		// default value
		$value = $this->value;
		
		// post/get data
		$data = $this->getMethod(true);

		// form submitted
		if($this->isSubmitted())
		{
			// submitted by post (may be empty)
			if(isset($data[$this->getName()])) $value = $data[$this->getName()];

			// html?
			if(!$allowHtml) $value = SpoonFilter::htmlentities($value);
		}

		return $value;
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
		
		// not filled
		if(!(isset($data[$this->getName()]) && trim($data[$this->getName()]) != ''))
		{
			if($error !== null) $this->addError($error);
			return false;
		}

		return true;
	}


	/**
	 * Parse the html for this button
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// not parsed
		if(!$this->parsed)
		{
			// name required
			if($this->getName() == '') throw new SpoonFormException('A name is required for a radiobutton. Please provide a valid name.');

			// value required
			if($this->getValue() == '') throw new SpoonFormException('A value is required for a radiobutton. Please provide a value.');

			// start html generation
			$this->html = '<input type="radio" id="'. $this->getId() .'" name="'. $this->getName() .'" value="'. $this->value .'"';

			// class / classOnError
			if($this->getClassAsHtml() != '') $this->html .= $this->getClassAsHtml();

			// style attribute
			if($this->style !== null) $this->html .= ' style="'. $this->getStyle() .'"';

			// tabindex attribute
			if($this->tabindex !== null) $this->html .= ' tabindex="'. $this->getTabIndex() .'"';

			// add javascript event functions
			if($this->getJavascriptAsHtml() != '') $this->html .= $this->getJavascriptAsHtml();

			// disabled attribute
			if($this->disabled) $this->html .= ' disabled="disabled"';

			// readonly
			if($this->readOnly) $this->html .= ' readonly="readonly"';

			// checked
			if($this->getChecked()) $this->html .= ' checked="checked"';

			// close input tag
			$this->html .= ' />';

			// parsed status
			$this->parsed = true;
		}
	}


	/**
	 * Change checked status
	 *
	 * @return	void
	 * @param	bool[optional] $checked
	 */
	public function setChecked($checked = true)
	{
		$this->checked = (bool) $checked;
	}


	/**
	 * Set the class on error
	 *
	 * @return	void
	 * @param	string $class
	 */
	public function setClassOnError($class)
	{
		$this->classError = (string) $class;
	}


	/**
	 * Overwrites the error stack
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
	}


	/**
	 * Set the value attribute
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