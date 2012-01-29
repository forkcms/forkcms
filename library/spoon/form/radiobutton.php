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
 * Creates a list of html radiobuttons
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormRadiobutton extends SpoonFormElement
{
	/**
	 * Should we allow external data
	 *
	 * @var	bool
	 */
	private $allowExternalData = false;


	/**
	 * Currently checked value
	 *
	 * @var	string
	 */
	private $checked;


	/**
	 * Errors stack
	 *
	 * @var	string
	 */
	private $errors;


	/**
	 * Name element
	 *
	 * @var	string
	 */
	private $name;


	/**
	 * List of labels and their values
	 *
	 * @var	string
	 */
	protected $values;


	/**
	 * List of variables
	 *
	 * @var	array
	 */
	private $variables;


	/**
	 * Class constructor.
	 *
	 * @param	string $name					The name.
	 * @param	array $values					The possible values. Each value should have a label and value-key.
	 * @param	string[optional] $checked		The value of the check radiobutton.
	 * @param	string[optional] $class			The CSS-class to be used.
	 */
	public function __construct($name, array $values, $checked = null, $class = 'inputRadiobutton')
	{
		// obligated fields
		$this->name = (string) $name;
		$this->setValues($values, $class);

		// custom optional fields
		if($checked !== null) $this->setChecked($checked);
	}


	/**
	 * Adds an error to the error stack.
	 *
	 * @param	string $error		The error message to set.
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
	 * @param	array $variables	The variables to convert into HTML-attributes.
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
	 * Retrieve the value of the checked item.
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
			if(isset($data[$this->getName()]) && isset($this->values[(string) $data[$this->getName()]]))
			{
				// set this field as checked
				$this->setChecked($data[$this->getName()]);
			}
		}

		return $this->checked;
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
	 * Retrieves the initial or submitted value.
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
			// allow external data
			if($this->allowExternalData) $value = $data[$this->name];

			// external data NOT allowed
			else
			{
				// item is set
				if(isset($data[$this->name]) && isset($this->values[(string) $data[$this->name]])) $value = $data[$this->name];
			}
		}

		return $value;
	}


	/**
	 * Checks if this field was submitted & filled.
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

			// correct
			if(isset($data[$this->name]) && isset($this->values[(string) $data[$this->name]])) return true;
		}

		// oh-oh
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Parse the html for this button.
	 *
	 * @return	array
	 * @param	SpoonTemplate[optional] $template	The template to parse the element in.
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name required
		if($this->name == '') throw new SpoonFormException('A name is required for a radiobutton. Please provide a name.');

		// loop values
		foreach($this->values as $value => $label)
		{
			// init vars
			$name = 'rbt' . SpoonFilter::toCamelCase($this->name);
			$element = array();
			$element[$name] = '<input type="radio" name="' . $this->name . '" value="' . $value . '"';

			// checked status
			if($value == $this->getChecked()) $element[$name] .= ' checked="checked"';

			// add attributes
			$element[$name] .= $this->getAttributesHTML($value, array('[id]' => $this->variables[$value]['id'], '[value]' => $value));

			// add variables to this element
			foreach($this->variables[$value] as $variableKey => $variableValue) $element[$variableKey] = $variableValue;

			// end input tag
			$element[$name] .= ' />';

			// clone into element
			$element['element'] = $element[$name];

			// add checkbox
			$radiobuttons[] = $element;
		}

		// template
		if($template !== null)
		{
			$template->assign($this->name, $radiobuttons);
			$template->assign('rbt' . SpoonFilter::toCamelCase($this->name) . 'Error', ($this->errors != '') ? '<span class="formError">' . $this->errors . '</span>' : '');
		}

		return $radiobuttons;
	}


	/**
	 * Set the checked value.
	 *
	 * @return	SpoonFormRadiobutton
	 * @param	string $checked		Set the radiobutton as checked.
	 */
	public function setChecked($checked)
	{
		if(!isset($this->values[(string) $checked]))
		{
			throw new SpoonFormException(sprintf('This value "%s" is not among the list of values.', (string) $checked));
		}

		$this->checked = (string) $checked;
		return $this;
	}


	/**
	 * Overwrites the error stack.
	 *
	 * @return	SpoonFormRadiobutton
	 * @param	string[optional] $error		The error message to set.
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
		return $this;
	}


	/**
	 * Set the labels and their values.
	 *
	 * @return	SpoonFormRadiobutton
	 * @param	array $values						The values to set.
	 * @param	string[optional] $defaultClass		The CSS-class to use.
	 */
	public function setValues(array $values, $defaultClass = 'inputRadio')
	{
		// empty values not allowed
		if(empty($values)) throw new SpoonFormException('The list with values should not be empty.');

		// loop values
		foreach($values as $value)
		{
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
