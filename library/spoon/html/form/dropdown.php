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
 * Generates a single or multiple dropdown menu.
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
 */
class SpoonDropDown extends SpoonVisualFormElement
{
	/**
	 * Class attribute on error
	 *
	 * @var	string
	 */
	protected $classError;


	/**
	 * Default element on top of the dropdown
	 *
	 * @var	array
	 */
	private $defaultElement = array();


	/**
	 * Errors stack
	 *
	 * @var	string
	 */
	protected $errors;


	/**
	 * Contains optgroups
	 *
	 * @var	bool
	 */
	private $optionGroups = false;


	/**
	 * Default selected item(s)
	 *
	 * @var	mixed
	 */
	private $selected;


	/**
	 * Whether you can select multiple elements
	 *
	 * @var	bool
	 */
	private $single = true;


	/**
	 * Number of elements shown at once
	 *
	 * @var	int
	 */
	private $size = 1;


	/**
	 * Initial values
	 *
	 * @var	array
	 */
	protected $values = array();


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	array $values
	 * @param	mixed[optional] $selected
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, array $values, $selected = null, $class = 'input-dropdown', $classError = 'input-dropdown-error')
	{
		// obligates fields
		$this->setId($name);
		$this->setName($name);
		$this->setValues($values);

		// custom optional fields
		if($selected !== null) $this->setSelected($selected);
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
		return $this->values;
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
	 * Retrieves the selected item(s)
	 *
	 * @return	mixed
	 */
	public function getSelected()
	{
		/**
		 * If we want to know what elements are selected, we first need
		 * to make sure that the $_POST/$_GET array is taken into consideration.
		 */

		// form submitted
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// multiple
			if(!$this->single)
			{
				// field has been submitted
				if(isset($data[$this->getName()]) && is_array($data[$this->getName()]) && count($data[$this->getName()]) != 0)
				{
					// reset selected
					$this->selected = array();

					// loop elements and add the value to the array
					foreach($data[$this->getName()] as $label => $value) $this->selected[] = $value;
				}
			}

			// single (has been submitted)
			elseif(isset($data[$this->getName()]) && $data[$this->getName()] != '') $this->selected = (string) $data[$this->getName()];
		}

		return $this->selected;
	}


	/**
	 * Retrieve the value(s)
	 *
	 * @return	mixed
	 */
	public function getValue()
	{
		// post/get data
		$data = $this->getMethod(true);

		// default values
		$values = $this->values;

		// submitted field
		if($this->isSubmitted() && isset($data[$this->getName()])) $values = $data[$this->getName()];
		return $values;
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
			if(!$this->single) $hasError = true;

			// single
			elseif(trim($data[$this->getName()]) == '') $hasError = true;
		}

		// has error
		if($hasError)
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Parses the html for this dropdown
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->getName() == '') throw new SpoonFormException('A name is required for a dropdown menu. Please provide a name.');

		// start html generation
		$output = "\r\n" . '<select id="'. $this->getId() .'" name="'. $this->getName();

		// multiple needs []
		if(!$this->single) $output .= '[]';

		// end name tag
		$output .= '"';

		// size (number of elements to be shown)
		if($this->size > 1) $output .= ' size="'. $this->size .'"';

		// multiple
		if(!$this->single) $output .= ' multiple="multiple"';

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

		// end select tag
		$output .= ">\r\n";

		// default element?
		if(count($this->defaultElement) != 0)
		{
			// create option
			$output .= "\t". '<option value="'. $this->defaultElement[1] .'"';

			// multiple
			if(!$this->single)
			{
				// if the value is within the selected items array
				if(is_array($this->getSelected()) && count($this->getSelected()) != 0 && in_array($this->defaultElement[1], $this->getSelected())) $output .= ' selected="selected"';
			}

			// single
			else
			{
				// if the current value is equal to the submitted value
				if($this->defaultElement[1] == $this->getSelected()) $output .= ' selected="selected"';
			}

			// end option
			$output .= ">". $this->defaultElement[0] ."</option>\r\n";
		}

		// has option groups
		if($this->optionGroups)
		{
			foreach ($this->values as $groupName => $group)
			{
				// create optgroup
				$output .= "\t" .'<optgroup label="'. $groupName .'">'."\n";

				// loop valuesgoo
				foreach ($group as $value => $label)
				{
					// create option
					$output .= "\t\t" . '<option value="'. $value .'"';

					// multiple
					if(!$this->single)
					{
						// if the value is within the selected items array
						if(is_array($this->getSelected()) && count($this->getSelected()) != 0 && in_array($value, $this->getSelected())) $output .= ' selected="selected"';
					}

					// single
					else
					{
						// if the current value is equal to the submitted value
						if($value == $this->getSelected()) $output .= ' selected="selected"';
					}

					// end option
					$output .= ">$label</option>\r\n";
				}

				// end optgroup
				$output .= "\t" .'</optgroup>'."\n";
			}
		}

		// regular dropdown
		else
		{
			// loop values
			foreach ($this->values as $value => $label)
			{
				// create option
				$output .= "\t". '<option value="'. $value .'"';

				// multiple
				if(!$this->single)
				{
					// if the value is within the selected items array
					if(is_array($this->getSelected()) && count($this->getSelected()) != 0 && in_array($value, $this->getSelected())) $output .= ' selected="selected"';
				}

				// single
				else
				{
					// if the current value is equal to the submitted value
					if($value == $this->getSelected()) $output .= ' selected="selected"';
				}

				// end option
				$output .= ">$label</option>\r\n";
			}
		}

		// end html
		$output .= "</select>\r\n";

		// parse to template
		if($template !== null)
		{
			$template->assign('ddm'. SpoonFilter::toCamelCase($this->name), $output);
			$template->assign('ddm'. SpoonFilter::toCamelCase($this->name) .'Error', ($this->errors!= '') ? '<span class="form-error">'. $this->errors .'</span>' : '');
		}

		// cough it up
		return $output;
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
	 * Sets the default element (top of the dropdown)
	 *
	 * @return	void
	 * @param	string $label
	 * @param	string[optional] $value
	 */
	public function setDefaultElement($label, $value = null)
	{
		$this->defaultElement = array((string) $label, (string) $value);
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
	public function setSingle($single = true)
	{
		$this->single = (bool) $single;
	}


	/**
	 * The number of elements that are shown at once
	 *
	 * @return	void
	 * @param	int[optional] $size
	 */
	public function setSize($size = 1)
	{
		$this->size = (int) $size;
	}


	/**
	 * Set the default selected item(s)
	 *
	 * @return	void
	 * @param	mixed $selected
	 */
	public function setSelected($selected)
	{
		// an array
		if(is_array($selected))
		{
			// may NOT be single
			if($this->single) throw new SpoonFormException('The "selected" argument must be a string, when you create a "single" dropdown');

			// arguments are fine
			foreach($selected as $item) $this->selected[] = $item;
		}

		// other types
		else
		{
			// single type
			if($this->single) $this->selected = (string) $selected;

			// multiple selections
			else $this->selected[] = (string) $selected;
		}
	}


	/**
	 * Sets the values for this dropdown menu
	 *
	 * @return	void
	 * @param	array $values
	 */
	private function setValues(array $values = array())
	{
		// has not items
		if(count($values) == 0) throw new SpoonFormException('The array with values contains no items.');

		// check the first element
		foreach($values as $value)
		{
			// dropdownfield with optgroups?
			$this->optionGroups = (is_array($value)) ? true : false;

			// break the loop
			break;
		}

		// has option groups
		if($this->optionGroups)
		{
			// loop each group
			foreach($values as $groupName => $options)
			{
				// loop each option
				foreach($options as $key => $value) $this->values[$groupName][$key] = $value;
			}
		}

		// no option groups
		else
		{
			// has items
			foreach($values as $label => $value) $this->values[$label] = $value;
		}
	}
}

?>