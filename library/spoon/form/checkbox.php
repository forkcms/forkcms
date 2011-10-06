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
 * Creates a single true/false checkbox.
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormCheckbox extends SpoonFormAttributes
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
	 * @param	string $name					The name.
	 * @param	bool[optional] $checked			Should the checkbox be checked?
	 * @param	string[optional] $class			The CSS-class to be used.
	 * @param	string[optional] $classError	The CSS-class to be used when there is an error.
	 */
	public function __construct($name, $checked = false, $class = 'inputCheckbox', $classError = 'inputCheckboxError')
	{
		// name & id
		$this->attributes['id'] = SpoonFilter::toCamelCase((string) $name, '_', true);
		$this->attributes['name'] = (string) $name;

		// custom optional fields
		$this->setChecked($checked);
		$this->attributes['class'] = (string) $class;
		$this->classError = (string) $classError;

		// update reserved attributes
		$this->reservedAttributes[] = 'checked';
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
	 * Retrieve the attributes as HTML.
	 *
	 * @return	string
	 * @param	array $variables	The variables to get the attribute-HTML for.
	 */
	protected function getAttributesHTML(array $variables)
	{
		// init var
		$html = '';

		// multiple
		if($this->getChecked()) $this->attributes['checked'] = 'checked';

		// loop attributes
		foreach($this->attributes as $key => $value)
		{
			// class?
			if($key == 'class') $html .= $this->getClassHTML();

			// other elements
			else $html .= ' ' . $key . '="' . str_replace(array_keys($variables), array_values($variables), $value) . '"';
		}

		return $html;
	}


	/**
	 * Returns the checked status for this checkbox.
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
			if(isset($data[$this->attributes['name']]) && $data[$this->attributes['name']] == 'Y') $checked = true;

			// adjust status
			$this->setChecked($checked);
		}

		return $this->checked;
	}


	/**
	 * Retrieves the class based on the errors status.
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
	 * Retrieve the errors.
	 *
	 * @return	string
	 */
	public function getErrors()
	{
		return $this->errors;
	}


	/**
	 * Retrieve the value(s).
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
			if(isset($data[$this->attributes['name']]) && $data[$this->attributes['name']] == 'Y') $value = true;
		}

		return $value;
	}


	/**
	 * Is this specific field checked.
	 *
	 * @return	bool
	 * @param	string[optional] $error		The error message to set.
	 */
	public function isChecked($error = null)
	{
		// checked
		if($this->getChecked()) return true;

		// not checked
		else
		{
			if($error !== null) $this->setError($error);
			return false;
		}
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

		// value submitted
		if(isset($data[$this->attributes['name']]) && $data[$this->attributes['name']] == 'Y') return true;

		// nothing submitted
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
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for checkbox. Please provide a name.');

		// start html generation
		$output = '<input type="checkbox" value="Y"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'])) . ' />';

		// template
		if($template !== null)
		{
			$template->assign('chk' . SpoonFilter::toCamelCase($this->attributes['name']), $output);
			$template->assign('chk' . SpoonFilter::toCamelCase($this->attributes['name']) . 'Error', ($this->errors != '') ? '<span class="formError"> ' . $this->errors . ' </span>' : '');
		}

		return $output;
	}


	/**
	 * Sets the checked status.
	 *
	 * @return	SpoonFormCheckbox
	 * @param	bool[optional] $checked		Should the element be checked?
	 */
	public function setChecked($checked = true)
	{
		$this->checked = (bool) $checked;
		return $this;
	}


	/**
	 * Overwrites the error stack.
	 *
	 * @return	SpoonFormCheckbox
	 * @param	string $error	The error message to set.
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
		return $this;
	}
}
