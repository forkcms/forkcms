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
 * Generates a checkbox.
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonCheckBox extends SpoonVisualFormElement
{
	/**
	 * Checked status
	 *
	 * @var	bool
	 */
	private $checked = false;


	/**
	 * Class attribute on error
	 *
	 * @var	string
	 */
	protected $classError;


	/**
	 * The default value to return when a single checkbox has not been checked
	 *
	 * @var	string
	 */
	private $defaultValue;


	/**
	 * Errors stack
	 *
	 * @var	string
	 */
	private $errors;


	/**
	 * Whether you can select multiple elements
	 *
	 * @var	bool
	 */
	private $single = true;


	/**
	 * Initial values
	 *
	 * @var	array
	 */
	private $value;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	mixed $value
	 * @param	bool[optional] $single
	 * @param	mixed[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classOnError
	 */
	public function __construct($name, $value, $single = false, $checked = false, $class = 'input-checkbox', $classError = 'input-checkbox-error')
	{
		// obligated fields
		$this->setSingle($single);

		// name & value
		$this->setName($name);
		$this->setValue($value);

		// id is based on the type
		$id = ($this->single) ? $name : $name .'_'. strtolower((string) $value);
		$this->setId($id);

		// custom optional fields
		$this->setChecked($checked);
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
	 * Returns the checked status for this checkbox
	 *
	 * @return	bool
	 */
	public function getChecked()
	{
		// form submitted
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);
			
			// not checked by default
			$checked = false;

			// multiple (is checked)
			if(!$this->single && isset($data[$this->getName()]) && is_array($data[$this->getName()]) && in_array($this->value, $data[$this->getName()])) $checked = true;

			// single (is checked)
			elseif(isset($data[$this->getName()]) && $data[$this->getName()] == $this->value) $checked = true;

			// adjust status
			$this->setChecked($checked);
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
	 * Retrieve wheter it's a multiple or single checkbox
	 *
	 * @return	bool
	 */
	public function getSingle()
	{
		return $this->single;
	}


	/**
	 * Retrieve the value(s)
	 *
	 * @return	mixed
	 */
	public function getValue()
	{
		// default value
		$value = $this->defaultValue;

		// submitted by post (may be empty)
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);
			
			// multple checkbox
			if(!$this->single && isset($data[$this->getName()])) $value = $data[$this->getName()];

			// single checkbox
			elseif(isset($data[$this->getName()]) && $data[$this->getName()] == $this->value) $value = $data[$this->getName()];
		}

		return $value;
	}
	
	
	/**
	 * Is this specific field checked
	 * 
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isChecked($error = null)
	{
		// checked
		if($this->getChecked()) return true;
		
		// not checked
		else 
		{
			if($error !== null) $this->addError($error);
			return false;
		}
	}


	/**
	 * Checks if this field was submitted & contains one more values
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);
		
		// default error
		$hasError = false;
		
		// value not submitted
		if(!isset($data[$this->getName()])) $hasError = true;

		// value submitted
		else
		{
			// multiple
			if(!$this->single)
			{
				// array with at least one result
				if(is_array($data[$this->getName()]) && count($data[$this->getName()]) != 0) $hasError = false;
				else $hasError = true;
			}

			// single
			else if(trim($data[$this->getName()]) == '') $hasError = true;
		}
		
		// has error
		if($hasError)
		{
			if($error !== null) $this->addError($error);
			return false;
		}

		return true;
	}


	/**
	 * Parses the html for this dropdown
	 *
	 * @return	string
	 */
	protected function parse()
	{
		// not yet parsed
		if(!$this->parsed)
		{
			// name required
			if($this->getName() == '') throw new SpoonFormException('A name is required for checkbox. Please provide a name.');

			// start html generation
			$this->html = '<input type="checkbox" id="'. $this->getId() .'" name="'. $this->getName();

			// multiple needs []
			if(!$this->single) $this->html .= '[]';

			// end name tag
			$this->html .= '"';

			// value
			$this->html .= ' value="'. $this->value .'"';
			
			// class / classOnError
			if($this->getClassAsHtml() != '') $this->html .= ' '. $this->getClassAsHtml();
			
			// style attribute
			if($this->style !== null) $this->html .= ' style="'. $this->getStyle() .'"';

			// tabindex
			if($this->tabindex !== null) $this->html .= ' tabindex="'. $this->getTabIndex() .'"';

			// readonly
			if($this->readOnly) $this->html .= ' readonly="readonly"';

			// add javascript methods
			if($this->getJavascriptAsHtml() != '') $this->html .= $this->getJavascriptAsHtml();

			// disabled
			if($this->disabled) $this->html .= ' disabled="disabled"';

			// checked or not?
			if($this->getChecked()) $this->html .= ' checked="checked"';

			// end input tag
			$this->html .= ' />';

			// update parse status
			$this->parsed = true;
		}
	}


	/**
	 * Sets the checked status
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
	 * Whether you can select one or more items
	 *
	 * @return	void
	 * @param	bool[optional] $single
	 */
	private function setSingle($single = false)
	{
		$this->single = (bool) $single;
	}


	/**
	 * Set the initial value
	 *
	 * @return	void
	 * @param	mixed $value
	 */
	public function setValue($value)
	{
		// single checkbox
		if($this->single)
		{
			// array with two items
			if(is_array($value))
			{
				// incorrect number of iteks
				if(count($value) != 2) throw new SpoonFormException('If you provide an array for a single checkbox, it has to contain exactly 2 elements.');

				// correct items
				else
				{
					// key 0 and 1 exist
					if(isset($value[0]) && isset($value[1]))
					{
						$this->value = (string) $value[0];
						$this->defaultValue = (string) $value[1];
					}

					// keys might be named or other numbers, which is not allowed
					else throw new SpoonFormException('If you provide an array for a single checkbox, it has to contain 2 elements, with indexes 0 and 1.');
				}
			}

			// regular string value
			else $this->value = (string) $value;
		}

		// multiple checkbox
		else $this->value = (string) $value;
	}
}

?>