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
 * Generates a checkbox.
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormMultiCheckbox extends SpoonFormElement
{
	/**
	 * Should we allow external data
	 *
	 * @var	bool
	 */
	private $allowExternalData = false;


	/**
	 * List of checked values
	 *
	 * @var	array
	 */
	private $checked = array();


	/**
	 * Errors stack
	 *
	 * @var	string
	 */
	private $errors;


	/**
	 * Element name
	 *
	 * @var	string
	 */
	private $name;


	/**
	 * Initial values
	 *
	 * @var	array
	 */
	private $values;


	/**
	 * List of custom variables
	 *
	 * @var	array
	 */
	private $variables;


	/**
	 * Class constructor.
	 *
	 * @param	string $name					The name.
	 * @param	array $values					The possible values. Each value should have a label and value-key.
	 * @param	mixed[optional] $checked		The value that should be checked.
	 * @param	string[optional] $class			The CSS-class to be used.
	 */
	public function __construct($name, array $values, $checked = null, $class = 'inputCheckbox')
	{
		// name & value
		$this->name = (string) $name;
		$this->setValues($values, $class);

		// custom optional fields
		if($checked !== null) $this->setChecked($checked);
		$this->classError = (string) $class;
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
	 * @param	string $element		The element.
	 * @param	array $variables	The variables that should be converted into HTML-attributes.
	 */
	private function getAttributesHTML($element, array $variables)
	{
		// init var
		$html = '';

		// has attributes
		if(isset($this->attributes[(string) $element]))
		{
			// loop attributes
			foreach($this->attributes[(string) $element] as $key => $value)
			{
				$html .= ' ' . $key . '="' . str_replace(array_keys($variables), array_values($variables), $value) . '"';
			}
		}

		return $html;
	}


	/**
	 * Retrieve the list of checked boxes.
	 *
	 * @return	array
	 */
	public function getChecked()
	{
		return ($this->isSubmitted()) ? $this->getValue() : $this->checked;
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
	 * Retrieves the name.
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * Fetch the list of values as provided in the constructor.
	 *
	 * @return	array
	 */
	public function getRawValues()
	{
		return $this->variables;
	}


	/**
	 * Retrieve the value(s).
	 *
	 * @return	array
	 */
	public function getValue()
	{
		// default value
		$values = array();

		// submitted by post (may be empty)
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// exists
			if(isset($data[$this->name]) && is_array($data[$this->name]))
			{
				// loop values
				foreach($data[$this->name] as $item)
				{
					// external data is allowed
					if($this->allowExternalData) $values[] = $item;

					// external data is NOT allowed
					else
					{
						// item exists
						if(isset($this->values[(string) $item])) $values[] = $item;
					}
				}
			}
		}

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
		// post/get data
		$data = $this->getMethod(true);

		// value submitted & is an array
		if(isset($data[$this->name]) && is_array($data[$this->name]))
		{
			// loop the elements until you can find one that is allowed
			foreach($data[$this->name] as $value)
			{
				if(isset($this->values[(string) $value])) return true;
			}
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
		// name required
		if($this->name == '') throw new SpoonFormException('A name is required for checkbox. Please provide a name.');

		// loop values
		foreach($this->values as $value => $label)
		{
			// init vars
			$name = 'chk' . SpoonFilter::toCamelCase($this->name);
			$element = array();
			$element[$name] = '<input type="checkbox" name="' . $this->name . '[]" value="' . $value . '"';

			// checked status
			if(in_array($value, $this->getChecked())) $element[$name] .= ' checked="checked"';

			// add attributes
			$element[$name] .= $this->getAttributesHTML($value, array('[id]' => $this->variables[$value]['id'], '[value]' => $value));

			// add variables to this element
			foreach($this->variables[$value] as $variableKey => $variableValue) $element[$variableKey] = $variableValue;

			// end input tag
			$element[$name] .= ' />';

			// clone into element
			$element['element'] = $element[$name];

			// add checkbox
			$checkboxes[] = $element;
		}

		// template
		if($template !== null)
		{
			$template->assign(SpoonFilter::toCamelCase($this->name, '_', true), $checkboxes);
			$template->assign('chk' . SpoonFilter::toCamelCase($this->name) . 'Error', ($this->errors != '') ? '<span class="formError">' . $this->errors . '</span>' : '');
		}

		return $checkboxes;
	}


	/**
	 * Should we allow external data.
	 *
	 * @param	bool[optional] $on	Is external data (through Javascript) allowed?
	 */
	public function setAllowExternalData($on = true)
	{
		$this->allowExternalData = (bool) $on;
	}


	/**
	 * Sets the checked status.
	 *
	 * @return	SpoonFormMultiCheckbox
	 * @param	mixed $checked		Set the value that should be checked.
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
		return $this;
	}


	/**
	 * Overwrites the error stack.
	 *
	 * @return	SpoonFormMultiCheckbox
	 * @param	string $error	The error message to set.
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
		return $this;
	}


	/**
	 * Set the initial values.
	 *
	 * @return	SpoonFormMultiCheckbox
	 * @param	array $values						The values.
	 * @param	string[optional] $defaultClass		The CSS-class to use.
	 */
	public function setValues(array $values, $defaultClass = 'inputCheckbox')
	{
		// empty values not allowed
		if(empty($values)) throw new SpoonFormException('The list with values should not be empty.');

		// loop values
		foreach($values as $value)
		{
			// additional array check
			if(!is_array($value)) throw new SpoonFormException('Each value should be an associative array with a "label" and "value" key.');

			// label is not set
			if(!isset($value['label'])) throw new SpoonFormException('Each element in this array should contain a key "label".');

			// value is not set
			if(!isset($value['value'])) throw new SpoonFormException('Each element in this array should contain a key "value".');

			// set value
			$this->values[(string) $value['value']] = (string) $value['label'];

			// attributes?
			if(isset($value['attributes']) && is_array($value['attributes']))
			{
				foreach($value['attributes'] as $attributeKey => $attributeValue) $this->attributes[$value['value']][(string) $attributeKey] = (string) $attributeValue;
			}

			// add default class
			if(!isset($this->attributes[$value['value']]['class'])) $this->attributes[$value['value']]['class'] = (string) $defaultClass;

			// variables
			if(isset($value['variables']) && is_array($value['variables']))
			{
				foreach($value['variables'] as $variableKey => $variableValue) $this->variables[$value['value']][(string) $variableKey] = (string) $variableValue;
			}

			// custom id
			if(!isset($this->variables[$value['value']]['id']))
			{
				if(isset($this->attributes[$value['value']]['id'])) $this->variables[$value['value']]['id'] = $this->attributes[$value['value']]['id'];
				else $this->variables[$value['value']]['id'] = SpoonFilter::toCamelCase($this->name . '_' . $value['value'], '_', true);
			}

			// add some custom vars
			if(!isset($this->variables[$value['value']]['label'])) $this->variables[$value['value']]['label'] = $value['label'];
			if(!isset($this->variables[$value['value']]['value'])) $this->variables[$value['value']]['value'] = $value['value'];

			// add id
			if(!isset($this->attributes[$value['value']]['id'])) $this->attributes[$value['value']]['id'] = $this->variables[$value['value']]['id'];
		}

		return $this;
	}
}
