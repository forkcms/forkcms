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
 * Creates a list of html radiobuttons
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
	 * Currently checked value
	 *
	 * @var	string
	 */
	private $checked;


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
	 * List of labels and their values
	 *
	 * @var	string
	 */
	protected $values;


	/**
	 * Class constructor
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string $values
	 * @param	string[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, array $values, $checked = null, $class = 'inputRadiobutton', $classError = 'inputRadiobuttonError')
	{
		// obligated fields
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
	 * Retrieve the value of the checked item
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
			if(isset($data[$this->getName()]) && isset($this->values[$data[$this->getName()]]))
			{
				// set this field as chceked
				$this->setChecked($data[$this->getName()]);
			}
		}

		return $this->checked;
	}


	/**
	 * Retrieves the class based on the errors status
	 *
	 * @return	string
	 */
	public function getClassAsHTML()
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
	 * This methid should not be used
	 *
	 * @return	void
	 */
	public function getId()
	{
		return;
	}


	/**
	 * Retrieves the initial or submitted value
	 *
	 * @return	string
	 */
	public function getValue()
	{
		// default value (may be null)
		$value = $this->getChecked();

		// post/get data
		$data = $this->getMethod(true);

		// form submitted
		if($this->isSubmitted())
		{
			// submitted by post (may be empty)
			if(isset($data[$this->getName()]) && isset($this->values[$data[$this->getName()]])) $value = $data[$this->getName()];
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

		// correct
		if(isset($data[$this->getName()]) && isset($this->values[$data[$this->getName()]])) return true;

		// oh-oh
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Parse the html for this button
	 *
	 * @return	array
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name required
		if($this->getName() == '') throw new SpoonFormException('A name is required for a radiobutton. Please provide a valid name.');

		// loop the values
		foreach($this->values as $value => $label)
		{
			// init vars
			$aElement['id'] = SpoonFilter::toCamelCase($this->getName() .'_'. $value, '_', true);
			$aElement['label'] = $label;
			$aElement['value'] = $value;
			$name = 'rbt'. SpoonFilter::toCamelCase($this->getName());
			$aElement[$name] = '';

			// start html generation
			$aElement[$name] = '<input type="radio" id="'. $aElement['id'] .'" name="'. $this->getName() .'" value="'. $value .'"';

			// class / classOnError
			if($this->getClassAsHTML() != '') $aElement[$name] .= $this->getClassAsHTML();

			// style attribute
			if($this->style !== null) $aElement[$name] .= ' style="'. $this->getStyle() .'"';

			// tabindex attribute
			if($this->tabindex !== null) $aElement[$name] .= ' tabindex="'. $this->getTabIndex() .'"';

			// add javascript event functions
			if($this->getJavascriptAsHTML() != '') $aElement[$name] .= $this->getJavascriptAsHTML();

			// disabled attribute
			if($this->disabled) $aElement[$name] .= ' disabled="disabled"';

			// readonly
			if($this->readOnly) $aElement[$name] .= ' readonly="readonly"';

			// checked
			if($this->getChecked() == $value) $aElement[$name] .= ' checked="checked"';

			// close input tag
			$aElement[$name] .= ' />';

			// add radiobutton
			$aRadioButton[] = $aElement;
		}

		// template
		if($template !== null)
		{
			$template->assign($this->getName(), $aRadioButton);
			$template->assign('rbt'. SpoonFilter::toCamelCase($this->getName()) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		// cough
		return $aRadioButton;
	}


	/**
	 * Set the checked value
	 *
	 * @return	void
	 * @param	string $checked
	 */
	public function setChecked($checked)
	{
		// doesnt exist
		if(!isset($this->values[(string) $checked])) throw new SpoonFormException('This value "'. (string) $checked .'" is not among the list of values.');

		// exists
		$this->checked = (string) $checked;
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
	 * Set the labels and their values
	 *
	 * @return	void
	 * @param	array $values
	 */
	private function setValues(array $values)
	{
		foreach($values as $value => $label) $this->values[(string) $value] = (string) $label;
	}
}

?>