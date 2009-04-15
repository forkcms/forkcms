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
require_once 'spoon/html/form/visual_element.php';


/**
 * The base class for every text input field
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonInputField extends SpoonVisualFormElement
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
	 * Maximum characters
	 *
	 * @var	int
	 */
	protected $maxlength;


	/**
	 * Initial value
	 *
	 * @var	string
	 */
	protected $value;


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
	 * Retrieve the initial value
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
	 * Retrieve the maxlength attribute
	 *
	 * @return	int
	 */
	public function getMaxlength()
	{
		return $this->maxlength;
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
	 * Overwrites the entire error stack
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
	}


	/**
	 * Set the maxlength attribute
	 *
	 * @return	void
	 * @param	int $characters
	 */
	public function setMaxlength($characters)
	{
		$this->maxlength = (int) $characters;
	}
}

?>