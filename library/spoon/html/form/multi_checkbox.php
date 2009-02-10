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
class SpoonMultiCheckBox extends SpoonVisualFormElement
{
	/**
	 * List of checked values
	 *
	 * @var	array
	 */
	private $checked = array();


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
	 * Initial values
	 *
	 * @var	array
	 */
	private $values;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	mixed $values
	 * @param	mixed[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, array $values, $checked = null, $class = 'input-checkbox', $classError = 'input-checkbox-error')
	{
		// name & value
		$this->setName($name);
		$this->setValues($values);

		// custom optional fields
		if($checked !== null) $this->setChecked($checked);
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
	 * Retrieve the list of checked boxes
	 *
	 * @return	array
	 */
	public function getChecked()
	{
		// when submitted
		if($this->isSubmitted()) return $this->getValue();

		// default values
		else return $this->checked;
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
	 * This methid should not be used
	 *
	 * @return	void
	 */
	public function getId()
	{
		return;
	}


	/**
	 * Retrieve the value(s)
	 *
	 * @return	array
	 */
	public function getValue()
	{
		// default value
		$aValues = array();

		// submitted by post (may be empty)
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// exists
			if(isset($data[$this->getName()]) && is_array($data[$this->getName()]))
			{
				// loop values
				foreach($data[$this->getName()] as $item)
				{
					// value exists
					if(isset($this->values[(string) $item])) $aValues[] = $item;
				}
			}

		}

		return $aValues;
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

		// value submitted & is an array
		if(isset($data[$this->getName()]) && is_array($data[$this->getName()]))
		{
			// loop the elements until you can find one that is allowed
			foreach($data[$this->getName()] as $value)
			{
				if(isset($this->values[(string) $value])) return true;
			}
		}

		// no values found
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

		// loop values
		foreach($this->values as $value => $label)
		{
			// init vars
			$aElement['id'] = $this->getName() .'_'. $value;
			$aElement['label'] = $label;
			$aElement['value'] = $value;
			$name = 'chk'. SpoonFilter::toCamelCase($this->getName());
			$aElement[$name] = '';

			// start html generation
			$aElement[$name] = '<input type="checkbox" id="'. $aElement['id'] .'" name="'. $this->getName() .'[]"';

			// value
			$aElement[$name] .= ' value="'. $value .'"';

			// class / classOnError
			if($this->getClassAsHtml() != '') $aElement[$name] .= ' '. $this->getClassAsHtml();

			// style attribute
			if($this->style !== null) $aElement[$name] .= ' style="'. $this->getStyle() .'"';

			// tabindex
			if($this->tabindex !== null) $aElement[$name] .= ' tabindex="'. $this->getTabIndex() .'"';

			// readonly
			if($this->readOnly) $aElement[$name] .= ' readonly="readonly"';

			// add javascript methods
			if($this->getJavascriptAsHtml() != '') $aElement[$name] .= $this->getJavascriptAsHtml();

			// disabled
			if($this->disabled) $aElement[$name] .= ' disabled="disabled"';

			// checked or not?
			if(in_array($aElement['value'], $this->getChecked())) $aElement[$name] .= ' checked="checked"';

			// end input tag
			$aElement[$name] .= ' />';

			// add checkbox
			$aCheckBox[] = $aElement;
		}

		// template
		if($template !== null)
		{
			$template->assign($this->getName(), $aCheckBox);
			$template->assign('chk'. SpoonFilter::toCamelCase($this->getName()) .'Error', ($this->errors!= '') ? '<span class="form-error">'. $this->errors .'</span>' : '');
		}

		// cough
		return $aCheckBox;
	}


	/**
	 * Sets the checked status
	 *
	 * @return	void
	 * @param	mixed $checked
	 */
	public function setChecked($checked)
	{
		// redefine
		$checked = (array) $checked;

		// loop values
		foreach($checked as $value)
		{
			// exists
			if(isset($this->values[(string) $value])) $aChecked[] = $value;
		}

		// set values
		if(isset($aChecked)) $this->checked = $aChecked;
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
	 * This method should not be used
	 *
	 * @return	void
	 * @param	string $id
	 */
	public function setId($id)
	{
		throw new SpoonFormException('This method is not to be used with this class.');
	}


	/**
	 * Set the initial values
	 *
	 * @return	void
	 * @param	mixed $values
	 */
	private function setValues(array $values)
	{
		foreach($values as $value => $label) $this->values[(string) $value] = (string) $label;
	}
}

?>