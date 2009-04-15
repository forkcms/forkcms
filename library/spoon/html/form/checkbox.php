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
	 * Errors stack
	 *
	 * @var	string
	 */
	private $errors;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	mixed[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, $checked = false, $class = 'inputCheckbox', $classError = 'inputCheckboxError')
	{
		// name & id
		$this->setName($name);
		$this->setId(SpoonFilter::toCamelCase($name, '_', true));

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

			// single (is checked)
			if(isset($data[$this->getName()]) && $data[$this->getName()] == 'Y') $checked = true;

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
	 * Retrieve the errors
	 *
	 * @return	string
	 */
	public function getErrors()
	{
		return $this->errors;
	}


	/**
	 * Retrieve the value(s)
	 *
	 * @return	mixed
	 */
	public function getValue()
	{
		// default value
		$value = false;

		// submitted by post (may be empty)
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// single checkbox
			if(isset($data[$this->getName()]) && $data[$this->getName()] == 'Y') $value = true;
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

		// value submitted
		if(isset($data[$this->getName()]) && $data[$this->getName()] == 'Y') return true;

		// nothing submitted
		if($error !== null) $this->addError($error);
		return false;
	}


	/**
	 * Parses the html for this dropdown
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name required
		if($this->getName() == '') throw new SpoonFormException('A name is required for checkbox. Please provide a name.');

		// start html generation
		$output = '<input type="checkbox" id="'. $this->getId() .'" name="'. $this->getName() .'" value="Y"';

		// class / classOnError
		if($this->getClassAsHtml() != '') $output .= ' '. $this->getClassAsHtml();

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

		// checked or not?
		if($this->getChecked()) $output .= ' checked="checked"';

		// end input tag
		$output .= ' />';

		// template
		if($template !== null)
		{
			$template->assign('chk'. SpoonFilter::toCamelCase($this->name), $output);
			$template->assign('chk'. SpoonFilter::toCamelCase($this->name) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		// cough
		return $output;
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
}

?>