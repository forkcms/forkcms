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
 * Generates a single or multiple dropdown menu.
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @author		Tijs Verkoyen <tijs@spoon-library.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		0.1.1
 */
class SpoonFormDropdown extends SpoonFormAttributes
{
	/**
	 * Should we allow external data
	 *
	 * @var	bool
	 */
	private $allowExternalData = false;


	/**
	 * Class attribute on error
	 *
	 * @var	string
	 */
	protected $classError;


	/**
	 * Default element on top of the dropdown (value, label)
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
	 * List of option specific attributes
	 *
	 * @var	array
	 */
	private $optionAttributes = array();


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
	 * Initial values
	 *
	 * @var	array
	 */
	protected $values = array();


	/**
	 * Class constructor.
	 *
	 * @param	string $name						The name.
	 * @param	array[optional] $values				The possible values. Each value should have a label and value-key.
	 * @param	mixed[optional] $selected			The selected value.
	 * @param	bool[optional] $multipleSelection	Can multiple elements be selected?
	 * @param	string[optional] $class				The CSS-class to be used.
	 * @param	string[optional] $classError		The CSS-class to be used when there is an error.
	 */
	public function __construct($name, array $values = null, $selected = null, $multipleSelection = false, $class = 'inputDropdown', $classError = 'inputDropdownError')
	{
		// obligates fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;
		$this->setValues($values);

		// update reserved attributes
		$this->reservedAttributes[] = 'multiple';

		// custom optional fields
		$this->single = !(bool) $multipleSelection;
		if($selected !== null) $this->setSelected($selected);
		$this->attributes['class'] = (string) $class;
		$this->classError = (string) $classError;
		if($this->single) $this->attributes['size'] = 1;
	}


	/**
	 * Adds an error to the error stack.
	 *
	 * @param	string $error	The error message to set.
	 */
	public function addError($error)
	{
		$this->errors .= (string) $error;
	}


	/**
	 * Retrieves the custom attributes as HTML.
	 *
	 * @return	string
	 * @param	array $variables	The variables to get the attributes-HTML for.
	 */
	protected function getAttributesHTML(array $variables)
	{
		// init var
		$html = '';

		// multiple
		if(!$this->single) $this->attributes['multiple'] = 'multiple';

		// loop attributes
		foreach($this->attributes as $key => $value)
		{
			// class?
			if($key == 'class') $html .= $this->getClassHTML();

			// name
			elseif($key == 'name' && !$this->single) $html .= ' name="' . $value . '"';

			// other elements
			else $html .= ' ' . $key . '="' . str_replace(array_keys($variables), array_values($variables), $value) . '"';
		}

		return $html;
	}


	/**
	 * Retrieve the class HTML.
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
	 * @param	string[optional] $key	The key to grab, of not provided all values will be returned.
	 */
	public function getDefaultValue($key = null)
	{
		// custom key
		if($key !== null)
		{
			// does this key exist?
			if(array_key_exists((string) $key, $this->values)) return $this->values[$key];

			// something went wrong
			throw new SpoonFormException('You can\'t fetch the value (' . $key . ') from the dropdown values based on its key if it doesn\'t exist.');
		}

		// everything by default
		return $this->values;
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
	 * Retrieve the list of option specific attributes by its' value.
	 *
	 * @return	array
	 * @param	string $value	The value to get the attributes for.
	 */
	public function getOptionAttributes($value)
	{
		return (isset($this->optionAttributes[(string) $value])) ? $this->optionAttributes[(string) $value] : array();
	}


	/**
	 * Retrieves the selected item(s).
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
				if(isset($data[$this->attributes['name']]) && is_array($data[$this->attributes['name']]) && count($data[$this->attributes['name']]) != 0)
				{
					// reset selected
					$this->selected = array();

					// loop elements and add the value to the array
					foreach($data[$this->attributes['name']] as $label => $value) $this->selected[] = $value;
				}
			}

			// single (has been submitted)
			elseif(isset($data[$this->attributes['name']]) && $data[$this->attributes['name']] != '') $this->selected = (string) $data[$this->attributes['name']];
		}

		return $this->selected;
	}


	/**
	 * Retrieve the value(s).
	 *
	 * @return	mixed
	 */
	public function getValue()
	{
		// post/get data
		$data = $this->getMethod(true);

		// loop initial values and fill the array of allowed values
		$allowedValues = array();
		foreach($this->values as $key => $value)
		{
			// the current key represents an optgroup
			if(is_array($value))
			{
				foreach($value as $key2 => $value2) $allowedValues[$key2] = $value2;
			}

			// single value
			else $allowedValues[$key] = $value;
		}

		// submitted field
		if($this->isSubmitted() && isset($data[$this->attributes['name']]))
		{
			// multiple selection allowed
			if(!$this->single)
			{
				// reset
				$values = array();

				// loop choices
				foreach((array) $data[$this->attributes['name']] as $value)
				{
					// external data is allowed
					if($this->allowExternalData) $values[] = $value;

					// external data is not allowed
					else
					{
						if((isset($allowedValues[$value]) || (isset($this->defaultElement[1]) && $this->defaultElement[1] == $value) && !in_array($value, $values))) $values[] = $value;
					}
				}
			}

			// ony single selection
			else
			{
				// rest
				$values = null;

				// external data is allowed
				if($this->allowExternalData) $values = (string) $data[$this->attributes['name']];

				// external data is NOT allowed
				else
				{
					if(isset($allowedValues[(string) $data[$this->attributes['name']]]) || (isset($this->defaultElement[1]) && $this->defaultElement[1] == $data[$this->attributes['name']] && $this->defaultElement[1] != '')) $values = (string) $data[$this->attributes['name']];
				}
			}
		}

		// form was submitted, but the field was not
		elseif($this->isSubmitted()) return null;

		// return calculated values
		return $values;
	}


	/**
	 * Checks if this field was submitted & contains one more values.
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isFilled($error = null)
	{
		// form submitted
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// single
			if($this->single)
			{
				// default element has value
				if(isset($this->defaultElement[1]) && trim($this->defaultElement[1]) != '')
				{
					// no value set and not equal to the default element
					if($this->getValue() === null && $data[$this->getName()] != $this->defaultElement[1])
					{
						if($error !== null) $this->setError($error);
						return false;
					}
				}

				// no default element or it has no value
				else
				{
					// something went wrong
					if($this->getValue() === null)
					{
						if($error !== null) $this->setError($error);
						return false;
					}
				}
			}

			// multiple
			else
			{
				// default element has value
				if(isset($this->defaultElement[1]) && trim($this->defaultElement[1]) != '')
				{
					// something went wrong
					if(!count($this->getValue()))
					{
						if($error !== null) $this->setError($error);
						return false;
					}
				}

				// no default element or it has no value
				else
				{
					// something went wrong
					if(!count($this->getValue()))
					{
						if($error !== null) $this->setError($error);
						return false;
					}
				}
			}

			// no problems
			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Parses the html for this dropdown.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template	The template to parse the element in.
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a dropdown menu. Please provide a name.');

		// name?
		if(!$this->single) $this->attributes['name'] .= '[]';

		// init var
		$selected = $this->getSelected();

		// start html generation
		$output = "\r\n" . '<select';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name']));

		// end select tag
		$output .= ">\r\n";

		// default element?
		if(count($this->defaultElement) != 0)
		{
			// create option
			$output .= "\t" . '<option value="' . $this->defaultElement[1] . '"';

			// multiple
			if(!$this->single)
			{
				// if the value is within the selected items array
				if(is_array($selected) && count($selected) != 0 && in_array($this->defaultElement[1], $selected)) $output .= ' selected="selected"';
			}

			// single
			else
			{
				// if the current value is equal to the submitted value
				if($this->defaultElement[1] == $selected && $selected !== null) $output .= ' selected="selected"';
			}

			// end option
			$output .= '>' . $this->defaultElement[0] . "</option>\r\n";
		}

		// loop all values
		foreach($this->values as $label => $value)
		{
			// skip the default element
			if(isset($this->defaultElement[1]) && $this->defaultElement[1] == $label) continue;

			// value is an optgroup?
			if($this->optionGroups[$label])
			{
				// create optgroup
				$output .= "\t" . '<optgroup label="' . $label . '">' . "\n";

				// loop value
				foreach($value as $key => $option)
				{
					// create option
					$output .= "\t\t" . '<option value="' . $key . '"';

					// multiple
					if(!$this->single)
					{
						// if the value is within the selected items array
						if(is_array($selected) && count($selected) != 0 && in_array($key, $selected)) $output .= ' selected="selected"';
					}

					// single
					else
					{
						// if the current value is equal to the submitted value
						if($key == $selected) $output .= ' selected="selected"';
					}

					// add custom attributes
					if(isset($this->optionAttributes[(string) $key]))
					{
						// loop each attribute
						foreach($this->optionAttributes[(string) $key] as $attrKey => $attrValue)
						{
							// add to the output
							$output .= ' ' . $attrKey . '="' . $attrValue . '"';
						}
					}

					// end option
					$output .= ">$option</option>\r\n";
				}

				// end optgroup
				$output .= "\t" . '</optgroup>' . "\n";
			}

			// no optgroup?
			else
			{
				// create option
				$output .= "\t" . '<option value="' . $label . '"';

				// multiple
				if(!$this->single)
				{
					// if the value is within the selected items array
					if(is_array($selected) && count($selected) != 0 && in_array($label, $selected)) $output .= ' selected="selected"';
				}

				// single
				else
				{
					// if the current value is equal to the submitted value
					if($this->getSelected() !== null && $label == $selected) $output .= ' selected="selected"';
				}

				// add custom attributes
				if(isset($this->optionAttributes[(string) $label]))
				{
					// loop each attribute
					foreach($this->optionAttributes[(string) $label] as $attrKey => $attrValue)
					{
						// add to the output
						$output .= ' ' . $attrKey . '="' . $attrValue . '"';
					}
				}

				// end option
				$output .= ">$value</option>\r\n";
			}
		}

		// end html
		$output .= "</select>\r\n";

		// parse to template
		if($template !== null)
		{
			$template->assign('ddm' . SpoonFilter::toCamelCase(str_replace('[]', '', $this->attributes['name'])), $output);
			$template->assign('ddm' . SpoonFilter::toCamelCase(str_replace('[]', '', $this->attributes['name'])) . 'Error', ($this->errors != '') ? '<span class="formError">' . $this->errors . '</span>' : '');
		}

		return $output;
	}


	/**
	 * Should we allow external data to be added.
	 *
	 * @return	SpoonFormDropdown
	 * @param	bool[optional] $on	Is external data allowed?
	 */
	public function setAllowExternalData($on = true)
	{
		$this->allowExternalData = (bool) $on;
		return $this;
	}


	/**
	 * Sets the default element (top of the dropdown).
	 *
	 * @return	SpoonFormDropdown
	 * @param	string $label				The label.
	 * @param	string[optional] $value		The value to use.
	 */
	public function setDefaultElement($label, $value = null)
	{
		$this->defaultElement = array((string) $label, (string) $value);
		if($value !== null) $this->values[$value] = (string) $label;
		return $this;
	}


	/**
	 * Overwrites the error stack.
	 *
	 * @return	SpoonFormDropdown
	 * @param	string $error	The error message to set.
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
		return $this;
	}


	/**
	 * Sets custom option attributes for a specific value.
	 *
	 * @return	SpoonFormDropdown
	 * @param	string $value		The value wherefor the attributes will be set.
	 * @param	array $attributes	The attributes to set.
	 */
	public function setOptionAttributes($value, array $attributes)
	{
		foreach($attributes as $attrKey => $attrValue)
		{
			$this->optionAttributes[(string) $value][(string) $attrKey] = (string) $attrValue;
		}

		return $this;
	}


	/**
	 * Set the default selected item(s).
	 *
	 * @return	SpoonFormDropdown
	 * @param	mixed $selected		Set the selected value.
	 */
	public function setSelected($selected)
	{
		// an array
		if(is_array($selected))
		{
			// may NOT be single
			if($this->single) throw new SpoonFormException('The "selected" argument must be a string, when you create a "single" dropdown');

			// arguments are fine
			foreach($selected as $item) $this->selected[] = (string) $item;
		}

		// other types
		else
		{
			// single type
			if($this->single) $this->selected = (string) $selected;

			// multiple selections
			else $this->selected[] = (string) $selected;
		}

		return $this;
	}


	/**
	 * Whether you can select one or more items.
	 *
	 * @return	SpoonFormDropdown
	 * @param	bool[optional] $single	Only selecting one element is allowed?
	 */
	public function setSingle($single = true)
	{
		$this->single = (bool) $single;
		return $this;
	}


	/**
	 * Sets the values for this dropdown menu.
	 *
	 * @param	array[optional] $values		The possible values. Each value should have a label and value-key.
	 */
	private function setValues(array $values = null)
	{
		// has no items
		if(count($values) == 0) $this->setDefaultElement('');

		// at least 1 item
		else
		{
			// check all elements
			foreach($values as $label => $value)
			{
				// dropdownfield with optgroups?
				$this->optionGroups[$label] = is_array($value);

				// is option group
				if($this->optionGroups[$label])
				{
					// loop value and assign its option
					foreach($value as $key => $option) $this->values[$label][$key] = $option;
				}

				// no option group
				else
				{
					// assign its value
					$this->values[$label] = $value;
				}
			}
		}
	}
}
