<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.be
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @author 		Tijs Verkoyen <tijs@spoon-library.be>
 * @author		Dave Lens <dave@spoon-library.be>
 * @since		0.1.1
 */


/** Filesystem package */
require_once 'spoon/filesystem/filesystem.php';


/**
 * This exception is used to handle form related exceptions.
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonFormException extends SpoonException {}


/**
 * The class that handles the forms
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonForm
{
	/**
	 * Action the form goes to
	 *
	 * @var	string
	 */
	private $action;


	/**
	 * Form status
	 *
	 * @var	bool
	 */
	private $correct = true;


	/**
	 * Errors (optional)
	 *
	 * @var	string
	 */
	private $errors;


	/**
	 * Allowed field in the $_POST or $_GET array
	 *
	 * @var	array
	 */
	private $fields = array();


	/**
	 * Form method
	 *
	 * @var	string
	 */
	private $method = 'post';


	/**
	 * Name of the form
	 *
	 * @var	string
	 */
	private $name;


	/**
	 * List of added objects
	 *
	 * @var	array
	 */
	private $objects = array();


	/**
	 * Extra parameters for the form tag
	 *
	 * @var	array
	 */
	private $parameters = array();


	/**
	 * Already validated?
	 *
	 * @var	bool
	 */
	private $validated = false;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $action
	 * @param	string[optional] $method
	 */
	public function __construct($name, $action = null, $method = 'post')
	{
		// required field
		$this->setName($name);
		$this->createHiddenField();

		// optional fields
		$this->setAction($action);
		$this->setMethod($method);
	}


	/**
	 * Add one or more objects to the stack.
	 *
	 * @return	void
	 * @param	object $object
	 */
	public function add($object)
	{
		// more than one argument
		if(func_num_args() != 0)
		{
			// iterate arguments
			foreach(func_get_args() as $argument)
			{
				// array of objects
				if(is_array($argument)) foreach($argument as $object) $this->add($object);

				// object
				else
				{
					// not an object
					if(!is_object($argument)) throw new SpoonFormException('The provided argument is not a valid object.');

					// valid object
					$this->objects[$argument->getName()] = $argument;
					$this->objects[$argument->getName()]->setFormName($this->name);
					$this->objects[$argument->getName()]->setMethod($this->method);

					// automagically add enctype if needed & not already added
					if($argument instanceof SpoonFileField && !isset($this->parameters['enctype'])) $this->setParameter('enctype', 'multipart/form-data');
				}
			}
		}
	}


	/**
	 * Adds a single button.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string $value
	 * @param	string[optional] $type
	 * @param	string[optional] $class
	 */
	public function addButton($name, $value, $type = null, $class = 'inputButton')
	{
		// add element
		$this->add(new SpoonButton($name, $value, $type, $class));

		// return the element
		return $this->getField($name);
	}


	/**
	 * Adds a single checkbox.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	bool[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addCheckBox($name, $checked = false, $class = 'inputCheckbox', $classError = 'inputCheckboxError')
	{
		// add element
		$this->add(new SpoonCheckBox($name, $checked, $class, $classError));

		// return element
		return $this->getField($name);
	}


	/**
	 * Adds one or more checkboxes.
	 *
	 * @return	void
	 */
	public function addCheckBoxes()
	{
		// loop fields
		foreach(func_get_args() as $argument)
		{
			// not an array
			if(!is_array($argument)) $this->add(new SpoonCheckBox($argument));

			// array
			else
			{
				foreach($argument as $name => $checked) $this->add(new SpoonCheckBox($name, (bool) $checked));
			}
		}
	}


	/**
	 * Adds a single datefield.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	int[optional] $value
	 * @param	string[optional] $mask
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addDateField($name, $value = null, $mask = null, $class = 'inputDatefield', $classError = 'inputDatefieldError')
	{
		// add element
		$this->add(new SpoonDateField($name, $value, $mask, $class, $classError));

		// return element
		return $this->getField($name);
	}


	/**
	 * Adds a single dropdown.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	array $values
	 * @param	string[optional] $selected
	 * @param	bool[optional] $multipleSelection
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addDropDown($name, array $values, $selected = null, $multipleSelection = false, $class = 'inputDropdown', $classError = 'inputDropdownError')
	{
		// add element
		$this->add(new SpoonDropDown($name, $values, $selected, $multipleSelection, $class, $classError));

		// return element
		return $this->getField($name);
	}


	/**
	 * Adds an error to the main error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function addError($error)
	{
		$this->errors .= trim((string) $error);
	}


	/**
	 * Adds a single file field.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addFileField($name, $class = 'inputFilefield', $classError = 'inputFilefieldError')
	{
		$this->add(new SpoonFileField($name, $class, $classError));
		return $this->getField($name);
	}


	/**
	 * Adds one or more file fields.
	 *
	 * @return	void
	 */
	public function addFileFields()
	{
		foreach(func_get_args() as $argument) $this->add(new SpoonFileField((string) $argument));
	}


	/**
	 * Adds a single hidden field.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 */
	public function addHiddenField($name, $value = null)
	{
		$this->add(new SpoonHiddenField($name, $value));
		return $this->getField($name);
	}


	/**
	 * Adds one or more hidden fields.
	 *
	 * @return	void
	 */
	public function addHiddenFields()
	{
		// loop fields
		foreach(func_get_args() as $argument)
		{
			// not an array
			if(!is_array($argument)) $this->add(new SpoonHiddenField($argument));

			// array
			else
			{
				foreach($argument as $name => $defaultValue) $this->add(new SpoonHiddenField($name, $defaultValue));
			}
		}
	}


	/**
	 * Adds a single image field.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addImageField($name, $class = 'inputFilefield', $classError = 'inputFilefieldError')
	{
		// add element
		$this->add(new SpoonImageField($name, $class, $classError));

		// return element
		return $this->getField($name);
	}


	/**
	 * Adds one or more image fields.
	 *
	 * @return	void
	 */
	public function addImageFields()
	{
		foreach(func_get_args() as $argument) $this->add(new SpoonImageField((string) $argument));
	}


	/**
	 * Adds a single multiple checkbox.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	array $values
	 * @param	bool[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addMultiCheckBox($name, array $values, $checked = null, $class = 'inputCheckbox', $classError = 'inputCheckboxError')
	{
		$this->add(new SpoonMultiCheckBox($name, $values, $checked, $class));
		return $this->getField($name);
	}


	/**
	 * Adds a single password field.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	int[optional] $maxlength
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $HTML
	 */
	public function addPasswordField($name, $value = null, $maxlength = null, $class = 'inputPassword', $classError = 'inputPasswordError', $HTML = false)
	{
		$this->add(new SpoonPasswordField($name, $value, $maxlength, $class, $classError, $HTML));
		return $this->getField($name);
	}


	/**
	 * Adds one or more password fields.
	 *
	 * @return	void
	 */
	public function addPasswordFields()
	{
		// loop fields
		foreach(func_get_args() as $argument)
		{
			// not an array
			if(!is_array($argument)) $this->add(new SpoonPasswordField($argument));

			// array
			else
			{
				foreach($argument as $name => $defaultValue) $this->add(new SpoonPasswordField($name, $defaultValue));
			}
		}
	}


	/**
	 * Adds a single radiobutton.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	array $values
	 * @param	string[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addRadioButton($name, array $values, $checked = null, $class = 'inputRadiobutton', $classError = 'inputRadiobuttonError')
	{
		$this->add(new SpoonRadioButton($name, $values, $checked, $class));
		return $this->getField($name);
	}


	/**
	 * Adds a single textarea.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $HTML
	 */
	public function addTextArea($name, $value = null, $class = 'inputTextarea', $classError = 'inputTextareaError', $HTML = false)
	{
		$this->add(new SpoonTextArea($name, $value, $class, $classError, $HTML));
		return $this->getField($name);
	}


	/**
	 * Adds one or more textareas.
	 *
	 * @return	void
	 */
	public function addTextAreas()
	{
		// loop fields
		foreach(func_get_args() as $argument)
		{
			// not an array
			if(!is_array($argument)) $this->add(new SpoonTextArea($argument));

			// array
			else
			{
				foreach($argument as $name => $defaultValue) $this->add(new SpoonTextArea($name, $defaultValue));
			}
		}
	}


	/**
	 * Adds a single textfield.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	int[optional] $maxlength
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $HTML
	 */
	public function addTextField($name, $value = null, $maxlength = null, $class = 'inputTextfield', $classError = 'inputTextfieldError', $HTML = false)
	{
		$this->add(new SpoonTextField($name, $value, $maxlength, $class, $classError, $HTML));
		return $this->getField($name);
	}


	/**
	 * Adds one or more textfields.
	 *
	 * @return	void
	 */
	public function addTextFields()
	{
		// loop fields
		foreach(func_get_args() as $argument)
		{
			// not an array
			if(!is_array($argument)) $this->add(new SpoonTextField($argument));

			// array
			else
			{
				foreach($argument as $name => $defaultValue) $this->add(new SpoonTextField($name, $defaultValue));
			}
		}
	}


	/**
	 * Adds a single timefield.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addTimeField($name, $value = null, $class = 'inputTimefield', $classError = 'inputTimefieldError')
	{
		$this->add(new SpoonTimeField($name, $value, $class, $classError));
		return $this->getField($name);
	}


	/**
	 * Adds one or more timefields.
	 *
	 * @return	void
	 */
	public function addTimeFields()
	{
		// loop fields
		foreach(func_get_args() as $argument)
		{
			// not an array
			if(!is_array($argument)) $this->add(new SpoonTimeField($argument));

			// array
			else
			{
				foreach($argument as $name => $defaultValue) $this->add(new SpoonTimeField($name, $defaultValue));
			}
		}
	}


	/**
	 * Loop all the fields and remove the ones that dont need to be in the form.
	 *
	 * @return	void
	 */
	public function cleanupFields()
	{
		// create list of fields
		foreach($this->objects as $object)
		{
			// file field should not be added since they are kept within the $_FILES
			if(!($object instanceof SpoonFileField)) $this->fields[] = $object->getName();
		}

		/**
		 * The form key should always automagically be added since the
		 * isSubmitted method counts on this field to check whether or
		 * not the form has been submitted
		 */
		if(!in_array('form', $this->fields)) $this->fields[] = 'form';

		// post method
		if($this->method == 'post')
		{
			// delete unwanted keys
			foreach($_POST as $key => $value) if(!in_array($key, $this->fields)) unset($_POST[$key]);

			// create needed keys
			foreach($this->fields as $field) if(!isset($_POST[$field])) $_POST[$field] = '';
		}

		// get method
		else
		{
			// delete unwanted keys
			foreach($_GET as $key => $value) if(!in_array($key, $this->fields)) unset($_GET[$key]);

			// create needed keys
			foreach($this->fields as $field) if(!isset($_GET[$field])) $_GET[$field] = '';
		}
	}


	/**
	 * Creates a hidden field & adds it to the form.
	 *
	 * @return	void
	 */
	private function createHiddenField()
	{
		$this->add(new SpoonHiddenField('form', $this->name));
	}


	/**
	 * Retrieve the action.
	 *
	 * @return	string
	 */
	public function getAction()
	{
		return $this->action;
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
	 * Fetches a field.
	 *
	 * @return	SpoonVisualFormElement
	 * @param	string $name
	 */
	public function getField($name)
	{
		// doesn't exist?
		if(!isset($this->objects[(string) $name])) throw new SpoonFormException('The field ('. (string) $name .') does not exist.');

		// all is fine
		return $this->objects[(string) $name];
	}


	/**
	 * Retrieve all fields.
	 *
	 * @return	array
	 */
	public function getFields()
	{
		return $this->objects;
	}


	/**
	 * Retrieve the method post/get.
	 *
	 * @return	string
	 */
	public function getMethod()
	{
		return $this->method;
	}


	/**
	 * Retrieve the name of this form.
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * Retrieve the parameters.
	 *
	 * @return	array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * Retrieve the parameters in html form.
	 *
	 * @return	string
	 */
	public function getParametersHTML()
	{
		// start html
		$HTML = '';

		// build & return html
		foreach($this->parameters as $key => $value) $HTML .= ' '. $key .'="'. $value .'"';
		return $HTML;
	}


	/**
	 * Generates an example template, based on the elements already added.
	 *
	 * @return	string
	 */
	public function getTemplateExample()
	{
		// start form
		$value = "\n";
		$value .= '{form:'. $this->name ."}\n";

		/**
		 * At first all the hidden fields need to be added to this form, since
		 * they're not shown and are best to be put right beneath the start of the form tag.
		 */
		foreach($this->objects as $object)
		{
			// is a hidden field
			if(($object instanceof SpoonHiddenField) && $object->getName() != 'form')
			{
				$value .= "\t". '{$hid'. SpoonFilter::toCamelCase($object->getName()) ."}\n";
			}
		}

		/**
		 * Add all the objects that are NOT hidden fields. Based on the existance of some methods
		 * errors will or will not be shown.
		 */
		foreach($this->objects as $object)
		{
			// NOT a hidden field
			if(!($object instanceof SpoonHiddenField))
			{
				// buttons
				if($object instanceof SpoonButton)
				{
					$value .= "\t<p>{\$btn". SpoonFilter::toCamelCase($object->getName()) ."}</p>\n";
				}

				// single checkboxes
				elseif($object instanceof SpoonCheckBox)
				{
					$value .= "\t". '<label for="'. $object->getAttribute('id') .'">'. SpoonFilter::toCamelCase($object->getName()) ."</label>\n";
					$value .= "\t<p>\n";
					$value .= "\t\t{\$chk". SpoonFilter::toCamelCase($object->getName()) ."}\n";
					$value .= "\t\t{\$chk". SpoonFilter::toCamelCase($object->getName()) ."Error}\n";
					$value .= "\t</p>\n";
				}

				// multi checkboxes
				elseif($object instanceof SpoonMultiCheckBox)
				{
					$value .= "\t<p>\n";
					$value .= "\t\t". SpoonFilter::toCamelCase($object->getName()) ."<br />\n";
					$value .= "\t\t{iteration:". $object->getName() ."}\n";
					$value .= "\t\t\t". '<label for="{$'. $object->getName() .'.id}">{$'. $object->getName() .'.chk'. SpoonFilter::toCamelCase($object->getName()) .'} {$'. $object->getName() .'.label}</label>' ."\n";
					$value .= "\t\t{/iteration:". $object->getName() ."}\n";
					$value .= "\t\t". '{$chk'. SpoonFilter::toCamelCase($object->getName()) ."Error}\n";
					$value .= "\t<p>\n";
				}

				// dropdowns
				elseif($object instanceof SpoonDropDown)
				{
					$value .= "\t". '<label for="'. $object->getAttribute('id') .'">'. SpoonFilter::toCamelCase($object->getName()) ."</label>\n";
					$value .= "\t<p>\n";
					$value .= "\t\t". '{$ddm'. SpoonFilter::toCamelCase($object->getName()) ."}\n";
					$value .= "\t\t". '{$ddm'. SpoonFilter::toCamelCase($object->getName()) ."Error}\n";
					$value .= "\t</p>\n";
				}

				// filefields
				elseif($object instanceof SpoonFileField)
				{
					$value .= "\t". '<label for="'. $object->getAttribute('id') .'">'. SpoonFilter::toCamelCase($object->getName()) ."</label>\n";
					$value .= "\t<p>\n";
					$value .= "\t\t". '{$file'. SpoonFilter::toCamelCase($object->getName()) ."}\n";
					$value .= "\t\t". '{$file'. SpoonFilter::toCamelCase($object->getName()) ."Error}\n";
					$value .= "\t</p>\n";
				}

				// radiobuttons
				elseif($object instanceof SpoonRadioButton)
				{
					$value .= "\t<p>\n";
					$value .= "\t\t". SpoonFilter::toCamelCase($object->getName()) ."<br />\n";
					$value .= "\t\t{iteration:". $object->getName() ."}\n";
					$value .= "\t\t\t". '<label for="{$'. $object->getName() .'.id}">{$'. $object->getName() .'.rbt'. SpoonFilter::toCamelCase($object->getName()) .'} {$'. $object->getName() .'.label}</label>' ."\n";
					$value .= "\t\t{/iteration:". $object->getName() ."}\n";
					$value .= "\t\t". '{$rbt'. SpoonFilter::toCamelCase($object->getName()) ."Error}\n";
					$value .= "\t<p>\n";
				}

				// textfields
				elseif(($object instanceof SpoonDateField) || ($object instanceof SpoonPasswordField) || ($object instanceof SpoonTextArea) || ($object instanceof SpoonTextField) || ($object instanceof SpoonTimeField))
				{
					$value .= "\t". '<label for="'. $object->getAttribute('id') .'">'. SpoonFilter::toCamelCase($object->getName()) ."</label>\n";
					$value .= "\t<p>\n";
					$value .= "\t\t". '{$txt'. SpoonFilter::toCamelCase($object->getName()) ."}\n";
					$value .= "\t\t". '{$txt'. SpoonFilter::toCamelCase($object->getName()) ."Error}\n";
					$value .= "\t</p>\n";
				}
			}
		}

		// close form tag
		return $value .'{/form:'. $this->name .'}';
	}


	/**
	 * Fetches all the values for this form as key/value pairs.
	 *
	 * @return	array
	 * @param	mixed[optional] $excluded
	 */
	public function getValues($excluded = null)
	{
		// redefine var
		$excluded = array();

		// has arguments
		if(func_num_args() != 0)
		{
			// iterate arguments
			foreach(func_get_args() as $argument)
			{
				if(is_array($argument)) foreach($argument as $value) $excluded[] = (string) $value;
				else $excluded[] = (string) $argument;
			}
		}

		// values
		$values = array();

		// loop objects
		foreach($this->objects as $object)
		{
			if(method_exists($object, 'getValue') && !in_array($object->getName(), $excluded)) $values[$object->getName()] = $object->getValue();
		}

		// return data
		return $values;
	}


	/**
	 * Returns the form's status.
	 *
	 * @return	bool
	 */
	public function isCorrect()
	{
		// not parsed
		if(!$this->validated) $this->validate();

		// return current status
		return $this->correct;
	}


	/**
	 * Returns whether this form has been submitted.
	 *
	 * @return	bool
	 */
	public function isSubmitted()
	{
		// default array
		$aForm = array();

		// post
		if($this->method == 'post' && isset($_POST)) $aForm = $_POST;

		// get
		elseif($this->method == 'get' && isset($_GET)) $aForm = $_GET;

		// name given
		if($this->name != '' && isset($aForm['form']) && $aForm['form'] == $this->name) return true;

		// no name given
		elseif($this->name == '' && $_SERVER['REQUEST_METHOD'] == strtoupper($this->method)) return true;

		// everything else
		return false;
	}


	/**
	 * Parse this form in the given template.
	 *
	 * @return	void
	 * @param	SpoonTemplate $template
	 */
	public function parse(SpoonTemplate $template)
	{
		// loop objects
		foreach($this->objects as $name => $object) $object->parse($template);

		// parse form tag
		$template->addForm($this);
	}


	/**
	 * Set the action.
	 *
	 * @return	void
	 * @param	string $action
	 */
	public function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Sets the correct value.
	 *
	 * @return	void
	 * @param	bool[optional] $correct
	 */
	private function setCorrect($correct = true)
	{
		$this->correct = (bool) $correct;
	}


	/**
	 * Set the form method.
	 *
	 * @return	void
	 * @param	string[optional] $method
	 */
	public function setMethod($method = 'post')
	{
		$this->method = SpoonFilter::getValue((string) $method, array('get', 'post'), 'post');
	}


	/**
	 * Set the name.
	 *
	 * @return	void
	 * @param	string $name
	 */
	private function setName($name)
	{
		$this->name = (string) $name;
	}


	/**
	 * Set a parameter for the form tag.
	 *
	 * @return	void
	 * @param	string $key
	 * @param	string $value
	 */
	public function setParameter($key, $value)
	{
		$this->parameters[(string) $key] = (string) $value;
	}


	/**
	 * Set multiple form parameters.
	 *
	 * @return	void
	 * @param	array $parameters
	 */
	public function setParameters(array $parameters)
	{
		foreach($parameters as $key => $value) $this->setParameter($key, $value);
	}


	/**
	 * Validates the form. This is an alternative for isCorrect, but without retrieve the status of course.
	 *
	 * @return	void
	 */
	public function validate()
	{
		// not parsed
        if(!$this->validated)
        {
        	// define errors
        	$errors = '';

			// loop objecjts
			foreach($this->objects as $oElement)
			{
				// check, since some objects don't have this method!
				if(method_exists($oElement, 'getErrors')) $errors .= $oElement->getErrors();
			}

			// affect correct status
			if(trim($errors) != '') $this->correct = false;

            // main form errors?
            if(trim($this->getErrors()) != '') $this->correct = false;

            // update parsed status
            $this->validated = true;
        }
	}
}


/**
 * The base class for every form element that wants to implement the standard
 * way for dealing with attributes
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonFormAttributes extends SpoonFormElement
{
	/**
	 * Retrieve the value for a specific attribute.
	 *
	 * @return	string
	 * @param	string $name
	 */
	public function getAttribute($name)
	{
		return (isset($this->attributes[(string) $name])) ? $this->attributes[(string) $name] : null;
	}


	/**
	 * Retrieves the custom attributes.
	 *
	 * @return	array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}


	/**
	 * Retrieves the custom attributes as HTML.
	 *
	 * @return	string
	 * @param	array $variables
	 */
	protected function getAttributesHTML(array $variables)
	{
		// init var
		$html = '';

		// loop attributes
		foreach($this->attributes as $key => $value)
		{
			// class?
			if($key == 'class' && method_exists($this, 'getClassHTML'))
			{
				$html .= $this->getClassHTML();
			}

			// other elements
			else $html .= ' '. $key .'="'. str_replace(array_keys($variables), array_values($variables), $value) .'"';
		}

		return $html;
	}


	/**
	 * Set a custom attribute and its value.
	 *
	 * @return	void
	 * @param	string $key
	 * @param	string $value
	 */
	public function setAttribute($key, $value)
	{
		// key is NOT allowed
		if(in_array(strtolower($key), $this->reservedAttributes)) throw new SpoonFormException('The key "'. $key .'" is a reserved attribute an can NOT be overwritten.');

		// set attribute
		$this->attributes[strtolower((string) $key)] = (string) $value;
	}


	/**
	 * Set multiple custom attributes at once.
	 *
	 * @return	void
	 * @param	array $attributes
	 */
	public function setAttributes(array $attributes)
	{
		foreach($attributes as $key => $value) $this->setAttribute($key, $value);
	}
}


/**
 * The base class for every form element
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonFormElement
{
	/**
	 * Custom attributes for this element
	 *
	 * @var	array
	 */
	protected $attributes = array();


	/**
	 * Name of the form this element is a part of
	 *
	 * @var	string
	 */
	protected $formName;


	/**
	 * Method inherited from the form (post/get)
	 *
	 * @var	string
	 */
	protected $method = 'post';


	/**
	 * Reserved attributes. Can not be overwritten using setAttribute(s)
	 *
	 * @var	array
	 */
	protected $reservedAttributes = array('type', 'name', 'value');


	/**
	 * Retrieve the form method or the submitted data.
	 *
	 * @return	string
	 * @param	bool[optional] $array
	 */
	public function getMethod($array = false)
	{
		if($array) return ($this->method == 'post') ? $_POST : $_GET;
		return $this->method;
	}


	/**
	 * Retrieve the unique name of this object.
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->attributes['name'];
	}


	/**
	 * Returns whether this form has been submitted.
	 *
	 * @return	bool
	 */
	public function isSubmitted()
	{
		// post/get data
		$data = $this->getMethod(true);

		// name given
		if($this->formName != null && isset($data['form']) && $data['form'] == $this->formName) return true;

		// no name given
		elseif($this->formName == null && $_SERVER['REQUEST_METHOD'] == strtoupper($this->method)) return true;

		// everything else
		else return false;
	}


	/**
	 * Parse the html for the current element.
	 *
	 * @return	void
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// filled by subclasses
	}


	/**
	 * Set the name of the form this field is a part of.
	 *
	 * @return	void
	 * @param	string $name
	 */
	public function setFormName($name)
	{
		$this->formName = (string) $name;
	}


	/**
	 * Set the form method.
	 *
	 * @return	void
	 * @param	string[optional] $method
	 */
	public function setMethod($method = 'post')
	{
		$this->method = SpoonFilter::getValue($method, array('get', 'post'), 'post');
	}
}


/**
 * The base class for every text input field
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonInputField extends SpoonFormAttributes
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
	 * Initial value
	 *
	 * @var	string
	 */
	protected $value;


	/**
	 * Adds an error to the error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function addError($error)
	{
		$this->errors .= (string) $error;
	}


	/**
	 * Retrieve the class html.
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
			if($this->attributes['class'] != '' && $this->classError != '') $value = ' class="'. $this->attributes['class'] .' '. $this->classError .'"';

			// only class defined
			elseif($this->attributes['class'] != '') $value = ' class="'. $this->attributes['class'] .'"';

			// only error defined
			elseif($this->classError != '') $value = ' class="'. $this->classError .'"';
		}

		// no errors
		else
		{
			// class defined
			if($this->attributes['class'] != '') $value = ' class="'. $this->attributes['class'] .'"';
		}

		return $value;
	}


	/**
	 * Retrieve the initial value.
	 *
	 * @return	string
	 */
	public function getDefaultValue()
	{
		return $this->value;
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
	 * Overwrites the entire error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
	}
}


/**
 * Creates an html form button
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonButton extends SpoonFormAttributes
{
	/**
	 * Button type (button, reset or submit)
	 *
	 * @var	string
	 */
	private $type = 'submit';


	/**
	 * Html value attribute
	 *
	 * @var	string
	 */
	private $value;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string $value
	 * @param	string[optional] $type
	 * @param	string[optional] $class
	 */
	public function __construct($name, $value, $type = null, $class = 'inputButton')
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;
		$this->value = (string) $value;

		// custom optional fields
		if($type !== null) $this->setType($type);
		$this->attributes['class'] = (string) $class;
	}


	/**
	 * Retrieve the initial value.
	 *
	 * @return	string
	 */
	public function getDefaultValue()
	{
		return $this->value;
	}


	/**
	 * Retrieves the button type.
	 *
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}


	/**
	 * Retrieves the value attribute.
	 *
	 * @return	string
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * Parse the html for this button.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// start element
		$output = '<input type="'. $this->type .'" value="'. $this->value .'"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'], '[value]' => $this->getValue())) .' />';

		// parse
		if($template !== null) $template->assign('btn'. SpoonFilter::toCamelCase($this->attributes['name']), $output);

		return $output;
	}


	/**
	 * Set the button type (button, reset or submit).
	 *
	 * @return	void
	 * @param	string[optional] $type
	 */
	public function setType($type = 'submit')
	{
		$this->type = SpoonFilter::getValue($type,  array('button', 'reset', 'submit'), 'submit');
	}
}


/**
 * Create an html filefield
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonFileField extends SpoonFormAttributes
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
	 * File extension
	 *
	 * @var	string
	 */
	private $extension;


	/**
	 * Filename (without extension)
	 *
	 * @var	string
	 */
	private $filename;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, $class = 'inputFilefield', $classError = 'inputFilefieldError')
	{
		// set name & id
		$this->attributes['id'] = SpoonFilter::toCamelCase((string) $name, '_', true);
		$this->attributes['name'] = (string) $name;


		// custom optional fields
		$this->attributes['class'] = (string) $class;
		$this->classError = (string) $classError;
	}


	/**
	 * Adds an error to the error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function addError($error)
	{
		$this->errors .= (string) $error;
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
			if($this->attributes['class'] != '' && $this->classError != '') $value = ' class="'. $this->attributes['class'] .' '. $this->classError .'"';

			// only class defined
			elseif($this->attributes['class'] != '') $value = ' class="'. $this->attributes['class'] .'"';

			// only error defined
			elseif($this->classError != '') $value = ' class="'. $this->classError .'"';
		}

		// no errors
		else
		{
			// class defined
			if($this->attributes['class'] != '') $value = ' class="'. $this->attributes['class'] .'"';
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
	 * Retrieve the extension of the uploaded file.
	 *
	 * @return	string
	 * @param	bool[optional] $lowercase
	 */
	public function getExtension($lowercase = true)
	{
		return $this->isFilled() ? (SpoonFile::getExtension($_FILES[$this->attributes['name']]['name'], $lowercase)) : '';
	}


	/**
	 * Retrieve the filename of the uploade file.
	 *
	 * @return	string
	 * @param	bool[optional] $includeExtension
	 */
	public function getFileName($includeExtension = true)
	{
		if($this->isFilled()) return (!$includeExtension) ? substr($_FILES[$this->attributes['name']]['name'], 0, strripos($_FILES[$this->attributes['name']]['name'], '.'. SpoonFile::getExtension($_FILES[$this->attributes['name']]['name'], false))) : $_FILES[$this->attributes['name']]['name'];
		return '';
	}


	/**
	 * Retrieve the filesize of the file in a specified unit.
	 *
	 * @return	int
	 * @param	string[optional] $unit
	 * @param	int[optional] $precision
	 */
	public function getFileSize($unit = 'kb', $precision = null)
	{
		if($this->isFilled())
		{
			// redefine unit
			$unit = SpoonFilter::getValue(strtolower($unit), array('b', 'kb', 'mb', 'gb'), 'kb');

			// fetch size
			$size = $_FILES[$this->attributes['name']]['size'];

			// redefine prection
			if($precision !== null) $precision = (int) $precision;

			// bytes
			if($unit == 'b') return $size;

			// kilobytes
			if($unit == 'kb') return round(($size / 1024), $precision);

			// megabytes
			if($unit == 'mb') return round(($size / 1024 / 1024), $precision);

			// gigabytes
			if($unit == 'gb') return round(($size / 1024 / 1024 / 1024), $precision);
		}

		return 0;
	}


	/**
	 * Get the temporary filename.
	 *
	 * @return	string
	 */
	public function getTempFileName()
	{
		return $this->isFilled() ? (string) $_FILES[$this->attributes['name']]['tmp_name'] : '';
	}


	/**
	 * Checks if the extension is allowed.
	 *
	 * @return	bool
	 * @param	array $extensions
	 * @param	string[optional] $error
	 */
	public function isAllowedExtension(array $extensions, $error = null)
	{
		// file has been uploaded
		if($this->isFilled())
		{
			// search for extension
			$return = in_array(strtolower(SpoonFile::getExtension($_FILES[$this->attributes['name']]['name'])), $extensions);

			// add error if needed
			if(!$return && $error !== null) $this->setError($error);

			// return
			return $return;
		}

		// no file uploaded
		else
		{
			// add error if needed
			if($error !== null) $this->setError($error);

			// return
			return false;
		}
	}


	/**
	 * Checks of the filesize is greater, equal or smaller than the given number + units.
	 *
	 * @return	bool
	 * @param	int $size
	 * @param	string[optional] $unit
	 * @param	string[optional] $operator
	 * @param	string[optional] $error
	 */
	public function isFileSize($size, $unit = 'kb', $operator = 'smaller', $error = null)
	{
		// file has been uploaded
		if($this->isFilled())
		{
			// define size
			$actualSize = $this->getFileSize($unit, 0);

			// operator
			$operator = SpoonFilter::getValue(strtolower($operator), array('smaller', 'equal', 'greater'), 'smaller');

			// smaller
			if($operator == 'smaller' && $actualSize < $size) return true;

			// equal
			if($operator == 'equal' && $actualSize == $size) return true;

			// greater
			if($operator == 'greater' && $actualSize > $size) return true;
		}

		// has error
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks for a valid file name (including dots but no slashes and other forbidden characters).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilename($error = null)
	{
		// correct filename
		if($this->isFilled() && SpoonFilter::isFilename($this->getFileName())) return true;

		// has error
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field was submitted & filled.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// default error
		$hasError = true;

		// form submitted
		if($this->isSubmitted())
		{
			// submitted, no errors & has a name!
			if(isset($_FILES[$this->attributes['name']]) && $_FILES[$this->attributes['name']]['error'] == 0 && $_FILES[$this->attributes['name']] != '') $hasError = false;
		}

		// has erorr?
		if($hasError)
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Attemps to move the uploaded file to the new location.
	 *
	 * @return	bool
	 * @param	string $path
	 * @param	int[optional] $chmod
	 */
	public function moveFile($path, $chmod = 0755)
	{
		// move the file
		$return = @move_uploaded_file($_FILES[$this->attributes['name']]['tmp_name'], (string) $path);

		// chmod file
		@chmod($path, $chmod);

		// return move file status
		return $return;
	}


	/**
	 * Parses the html for this filefield.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a file field. Please provide a name.');

		// start html generation
		$output = '<input type="file"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'])) .' />';

		// parse to template
		if($template !== null)
		{
			$template->assign('file'. SpoonFilter::toCamelCase($this->attributes['name']), $output);
			$template->assign('file'. SpoonFilter::toCamelCase($this->attributes['name']) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		return $output;
	}


	/**
	 * Set the class on error.
	 *
	 * @return	void
	 * @param	string $class
	 */
	public function setClassOnError($class)
	{
		$this->classError = (string) $class;
	}


	/**
	 * Overwrites the error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
	}
}


/**
 * Create an html filefield specific for images
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Tijs Verkoyen <tijs@spoon-library.be>
 * @since		1.1.3
 */
class SpoonImageField extends SpoonFileField
{
	/**
	 * Retrieve the extension of the uploaded file (based on the MIME-type).
	 *
	 * @return	string
	 * @param	bool[optional] $lowercase
	 */
	public function getExtension($lowercase = true)
	{
		if($this->isSubmitted())
		{
			// get image properties
			$properties = @getimagesize($_FILES[$this->attributes['name']]['tmp_name']);

			// validate properties
			if($properties !== false)
			{
				// get extension
				$extension = image_type_to_extension($properties[2], false);

				// cleanup
				if($extension == 'jpeg') $extension = 'jpg';

				// return
				return ((bool) $lowercase) ? strtolower($extension) : $extension;
			}

			// no image
			return '';
		}

		// fallback
		return '';
	}


	/**
	 * Checks if this field was submitted an if it is an image check if the dimensions are ok,
	 * if the submitted file wasn't an image it will return false.
	 *
	 * @return	bool
	 * @param	int $width
	 * @param	int $height
	 * @param	string[optional] $error
	 */
	public function hasMinimumDimensions($width, $height, $error = null)
	{
		// default error
		$hasError = true;

		// form submitted
		if($this->isSubmitted())
		{
			// get image properties
			$properties = @getimagesize($_FILES[$this->attributes['name']]['tmp_name']);

			// valid properties
			if($properties !== false)
			{
				// redefine
				$actualWidth = (int) $properties[0];
				$actualHeight = (int) $properties[1];

				// validate width and height
				if($actualWidth >= $width && $actualHeight >= $height) $hasError = false;
			}
		}

		// has erorr?
		if($hasError)
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if the mime-type is allowed.
	 * @see	http://www.w3schools.com/media/media_mimeref.asp
	 *
	 * @return	bool
	 * @param	array $allowedTypes
	 * @param	string[optional] $error
	 */
	public function isAllowedMimeType(array $allowedTypes, $error = null)
	{
		// file has been uploaded
		if($this->isFilled())
		{
			// get image properties
			$properties = @getimagesize($_FILES[$this->attributes['name']]['tmp_name']);

			// invalid properties
			if($properties === false) $return = false;

			// search for mime-type
			else $return = in_array($properties['mime'], $allowedTypes);

			// add error if needed
			if(!$return && $error !== null) $this->setError($error);

			// return
			return $return;
		}

		// no file uploaded
		else
		{
			// add error if needed
			if($error !== null) $this->setError($error);

			// return
			return false;
		}
	}
}


/**
 * Creates a list of html radiobuttons
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonRadioButton extends SpoonFormElement
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
	 * @return	void
	 * @param	string $name
	 * @param	string $values
	 * @param	string[optional] $checked
	 * @param	string[optional] $class
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
	 * @return	void
	 * @param	string $error
	 */
	public function addError($error)
	{
		$this->errors .= (string) $error;
	}


	/**
	 * Retrieves the custom attributes as HTML.
	 *
	 * @return	string
	 * @param	string $element
	 * @param	array $variables
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
				$html .= ' '. $key .'="'. str_replace(array_keys($variables), array_values($variables), $value) .'"';
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
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// form submitted
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// correct
			if(isset($data[$this->name]) && isset($this->values[$data[$this->name]])) return true;
		}

		// oh-oh
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Parse the html for this button.
	 *
	 * @return	array
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name required
		if($this->name == '') throw new SpoonFormException('A name is required for a radiobutton. Please provide a name.');

		// loop values
		foreach($this->values as $value => $label)
		{
			// init vars
			$name = 'rbt'. SpoonFilter::toCamelCase($this->name);
			$element = array();
			$element[$name] = '<input type="radio" name="'. $this->name .'" value="'. $value .'"';

			// checked status
			if($value == $this->getChecked()) $element[$name] .= ' checked="checked"';

			// add attributes
			$element[$name] .= $this->getAttributesHTML($value, array('[id]' => $this->variables[$value]['id'], '[value]' => $value));

			// add variables to this element
			foreach($this->variables[$value] as $variableKey => $variableValue) $element[$variableKey] = $variableValue;

			// end input tag
			$element[$name] .= ' />';

			// add checkbox
			$radioButtons[] = $element;
		}

		// template
		if($template !== null)
		{
			$template->assign($this->name, $radioButtons);
			$template->assign('chk'. SpoonFilter::toCamelCase($this->name) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		return $radioButtons;
	}


	/**
	 * Set the checked value.
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
	 * Overwrites the error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
	}


	/**
	 * Set the labels and their values.
	 *
	 * @return	void
	 * @param	array $values
	 */
	private function setValues(array $values, $defaultClass = 'inputRadiobutton')
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
				else $this->variables[$value['value']]['id'] = SpoonFilter::toCamelCase($this->name . '_'. $value['value'], '_', true);
			}

			// add some custom vars
			if(!isset($this->variables[$value['value']]['label'])) $this->variables[$value['value']]['label'] = $value['label'];
			if(!isset($this->variables[$value['value']]['value'])) $this->variables[$value['value']]['value'] = $value['value'];

			// add id
			if(!isset($this->attributes[$value['value']]['id'])) $this->attributes[$value['value']]['id'] = $this->variables[$value['value']]['id'];
		}
	}
}


/**
 * Creates an html textfield (date field)
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonDateField extends SpoonInputField
{
	/**
	 * Input mask (every item may only occur once)
	 *
	 * @var	string
	 */
	protected $mask = 'd-m-Y';


	/**
	 * The value needed to base the mask on
	 *
	 * @var	int
	 */
	private $defaultValue;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	int[optional] $value
	 * @param	string[optional] $mask
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, $value = null, $mask = null, $class = 'inputDatefield', $classError = 'inputDatefieldError')
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;

		/**
		 * The input mask defines the maxlength attribute, therefor
		 * this needs to be set anyhow. The mask needs to be updated
		 * before the value is set, or the old mask (in case it differs)
		 * will automatically be used.
		 */
		$this->setMask(($mask !== null) ? $mask : $this->mask);

		/**
		 * The value will be filled based on the default input mask
		 * if no value has been defined.
		 */
		$this->defaultValue = ($value !== null) ? (int) $value : time();
		$this->setValue($this->defaultValue);

		// custom optional fields
		$this->attributes['class'] = (string) $class;
		$this->classError = (string) $classError;

		// update reserved attributes
		$this->reservedAttributes[] = 'maxlength';
	}


	/**
	 * Retrieve the initial value.
	 *
	 * @return	string
	 */
	public function getDefaultValue()
	{
		return $this->value;
	}


	/**
	 * Retrieve the input mask.
	 *
	 * @return	string
	 */
	public function getMask()
	{
		return $this->mask;
	}


	/**
	 * Returns a timestamp based on mask & optional fields.
	 *
	 * @return	int
	 * @param	int[optional] $year
	 * @param	int[optional] $month
	 * @param	int[optional] $day
	 * @param	int[optional] $hour
	 * @param	int[optional] $minute
	 * @param	int[optional] $second
	 */
	public function getTimestamp($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null)
	{
		// field has been filled in
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// valid field
			if($this->isValid())
			{
				// define long mask
				$longMask = str_replace(array('d', 'm', 'Y'), array('dd', 'mm', 'yyyy'), $this->mask);

				// year found
				if(strpos($longMask, 'yyyy') !== false && $year === null)
				{
					// redefine year
					$year = substr($data[$this->attributes['name']], strpos($longMask, 'yyyy'), 4);
				}

				// month found
				if(strpos($longMask, 'mm') !== false && $month === null)
				{
					// redefine month
					$month = substr($data[$this->attributes['name']], strpos($longMask, 'mm'), 2);
				}

				// day found
				if(strpos($longMask, 'dd') !== false && $day === null)
				{
					// redefine day
					$day = substr($data[$this->attributes['name']], strpos($longMask, 'dd'), 2);
				}
			}

			// init vars
			$year = ($year !== null) ? (int) $year : (int) date('Y');
			$month = ($month !== null) ? (int) $month : (int) date('n');
			$day = ($day !== null) ? (int) $day : (int) date('j');
			$hour = ($hour !== null) ? (int) $hour : (int) date('H');
			$minute = ($minute !== null) ? (int) $minute : (int) date('i');
			$second = ($second !== null) ? (int) $second : (int) date('s');
		}

		// create (default) time
		return mktime($hour, $minute, $second, $month, $day, $year);
	}


	/**
	 * Retrieve the initial or submitted value.
	 *
	 * @return	string
	 */
	public function getValue()
	{
		// redefine html & value
		$value = $this->value;

		// added to form
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// submitted by post (may be empty)
			if(isset($data[$this->attributes['name']]))
			{
				// value
				$value = $data[$this->attributes['name']];
			}
		}

		return $value;
	}


	/**
	 * Checks if this field has any content (except spaces).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// form submitted
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// check filled status
			if(!(isset($data[$this->getName()]) && trim($data[$this->getName()]) != ''))
			{
				if($error !== null) $this->setError($error);
				return false;
			}
		}

		return true;
	}


	/**
	 * Checks if this field is correctly submitted.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isValid($error = null)
	{
		// field has been filled in
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// maxlength checks out (needs to be equal)
			if(strlen($data[$this->attributes['name']]) == $this->attributes['maxlength'])
			{
				// define long mask
				$longMask = str_replace(array('d', 'm', 'y', 'Y'), array('dd', 'mm', 'yy', 'yyyy'), $this->mask);

				// init vars
				$year = (int) date('Y');
				$month = (int) date('m');
				$day = (int) date('d');

				// validate year (yyyy)
				if(strpos($longMask, 'yyyy') !== false)
				{
					// redefine year
					$year = substr($data[$this->attributes['name']], strpos($longMask, 'yyyy'), 4);

					// not an int
					if(!SpoonFilter::isInteger($year)) { $this->setError($error); return false; }

					// invalid year
					if(!checkdate(1, 1, $year)) { $this->setError($error); return false; }
				}

				// validate year (yy)
				if(strpos($longMask, 'yy') !== false && strpos($longMask, 'yyyy') === false)
				{
					// redefine year
					$year = substr($data[$this->attributes['name']], strpos($longMask, 'yy'), 2);

					// not an int
					if(!SpoonFilter::isInteger($year)) { $this->setError($error); return false; }

					// invalid year
					if(!checkdate(1, 1, '19'. $year)) { $this->setError($error); return false; }
				}

				// validate month (mm)
				if(strpos($longMask, 'mm') !== false)
				{
					// redefine month
					$month = substr($data[$this->attributes['name']], strpos($longMask, 'mm'), 2);

					// not an int
					if(!SpoonFilter::isInteger($month)) { $this->setError($error); return false; }

					// invalid month
					if(!checkdate($month, 1, $year)) { $this->setError($error); return false; }
				}

				// validate day (dd)
				if(strpos($longMask, 'dd') !== false)
				{
					// redefine day
					$day = substr($data[$this->attributes['name']], strpos($longMask, 'dd'), 2);

					// not an int
					if(!SpoonFilter::isInteger($day)) { $this->setError($error); return false; }

					// invalid day
					if(!checkdate($month, $day, $year)) { $this->setError($error); return false; }
				}
			}

			// maximum length doesn't check out
			else { $this->setError($error); return false; }
		}

		// not filled out
		else { $this->setError($error); return false; }

		/**
		 * When the code reaches the point, it means no errors have occured
		 * and truth will out!
		 */
		return true;
	}


	/**
	 * Parses the html for this date field.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a date field. Please provide a valid name.');

		// start html generation
		$output = '<input type="text" value="'. $this->getValue() .'"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'], '[value]' => $this->getValue())) .' />';

		// template
		if($template !== null)
		{
			$template->assign('txt'. SpoonFilter::toCamelCase($this->attributes['name']), $output);
			$template->assign('txt'. SpoonFilter::toCamelCase($this->attributes['name']) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		return $output;
	}


	/**
	 * Set the input mask.
	 *
	 * @return	void
	 * @param	string[optional] $mask
	 */
	public function setMask($mask = null)
	{
		// redefine mask
		$mask = ($mask !== null) ? (string) $mask : $this->mask;

		// allowed characters
		$aCharachters = array('.', '-', '/', 'd', 'm', 'y', 'Y');

		// new mask
		$maskCorrected = '';

		// loop all elements
		for($i = 0; $i < strlen($mask); $i++)
		{
			// char allowed
			if(in_array(substr($mask, $i, 1), $aCharachters)) $maskCorrected .= substr($mask, $i, 1);
		}

		// new mask
		$this->mask = $maskCorrected;

		// define maximum length for this element
		$maskCorrected = str_replace(array('d', 'm', 'y', 'Y'), array('dd', 'mm', 'yy', 'yyyy'), $maskCorrected);

		// update maxium length
		$this->attributes['maxlength'] = strlen($maskCorrected);

		// update value
		if($this->defaultValue !== null) $this->setValue($this->defaultValue);
	}


	/**
	 * Set the value attribute for this date field.
	 *
	 * @return	void
	 * @param	int $value
	 */
	private function setValue($value)
	{
		$this->value = date($this->mask, (int) $value);
	}
}


/**
 * Create an html textfield
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonTextField extends SpoonInputField
{
	/**
	 * Is the content of this field html?
	 *
	 * @var	bool
	 */
	private $isHTML = false;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	int[optional] $maxlength
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $HTML
	 */
	public function __construct($name, $value = null, $maxlength = null, $class = 'inputTextfield', $classError = 'inputTextfieldError', $HTML = false)
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;

		// custom optional fields
		if($value !== null) $this->value = (string) $value;
		if($maxlength !== null) $this->attributes['maxlength'] = (int) $maxlength;
		$this->attributes['class'] = (string) $class;
		if($classError !== null) $this->classError = (string) $classError;
		$this->isHTML = (bool) $HTML;
	}


	/**
	 * Retrieve the initial or submitted value.
	 *
	 * @return	string
	 * @param	bool[optional] $allowHTML
	 */
	public function getValue($allowHTML = null)
	{
		// redefine html & default value
		$allowHTML = ($allowHTML !== null) ? (bool) $allowHTML : $this->isHTML;
		$value = $this->value;

		// contains html
		if($this->isHTML)
		{
			// set value
			$value = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($value) : SpoonFilter::htmlentities($value);
		}

		// form submitted
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// submitted by post (may be empty)
			if(isset($data[$this->getName()]))
			{
				// value
				$value = $data[$this->attributes['name']];

				// maximum length?
				if(isset($this->attributes['maxlength']) && $this->attributes['maxlength'] > 0) $value = mb_substr($value, 0, (int) $this->attributes['maxlength'], SPOON_CHARSET);

				// html allowed?
				if(!$allowHTML) $value = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($value) : SpoonFilter::htmlentities($value);
			}
		}

		return $value;
	}


	/**
	 * Checks if this field contains only letters a-z and A-Z.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isAlphabetical($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isAlphabetical($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field only contains letters & numbers (without spaces).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isAlphaNumeric($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isAlphaNumeric($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if the field is between a given minimum and maximum (includes min & max).
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	int $maximum
	 * @param	string[optional] $error
	 */
	public function isBetween($minimum, $maximum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isBetween($minimum, $maximum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks this field for a boolean (true/false | 0/1).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isBool($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isBool($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field only contains numbers 0-9.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isDigital($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isDigital($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks this field for a valid e-mail address.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isEmail($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isEmail($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// has error
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks for a valid file name (including dots but no slashes and other forbidden characters).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilename($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isFilename($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// has error
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field was submitted & filled.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!(isset($data[$this->attributes['name']]) && trim($data[$this->attributes['name']]) != ''))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks this field for numbers 0-9 and an optional - (minus) sign (in the beginning only).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFloat($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isFloat($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field is greater than another value.
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	string[optional] $error
	 */
	public function isGreaterThan($minimum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isGreaterThan($minimum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks this field for numbers 0-9 and an optional - (minus) sign (in the beginning only).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isInteger($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isInteger($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field is a proper ip address.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isIp($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isIp($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field does not exceed the given maximum.
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param	int[optional] $error
	 */
	public function isMaximum($maximum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMaximum($maximum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field's length is less (or equal) than the given maximum.
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param	string[optional] $error
	 */
	public function isMaximumCharacters($maximum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMaximumCharacters($maximum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field is at least a given minimum.
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	string[optional] $error
	 */
	public function isMinimum($minimum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMinimum($minimum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field's length is more (or equal) than the given minimum.
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	string[optional] $error
	 */
	public function isMinimumCharacters($minimum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMinimumCharacters($minimum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Alias for isDigital (Field may only contain numbers 0-9).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isNumeric($error = null)
	{
		return $this->isDigital($error);
	}


	/**
	 * Checks if the field is smaller than a given maximum.
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param	string[optional] $error
	 */
	public function isSmallerThan($maximum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isSmallerThan($maximum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field contains any string that doesn't have control characters (ASCII 0 - 31) but spaces are allowed.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isString($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isString($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks this field for a valid url.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isURL($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isURL($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if the field validates against the regexp.
	 *
	 * @return	bool
	 * @param	string $regexp
	 * @param	string[optional] $error
	 */
	public function isValidAgainstRegexp($regexp, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isValidAgainstRegexp((string) $regexp, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Parses the html for this textfield.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a textfield. Please provide a name.');

		// start html generation
		$output = '<input type="text" value="'. $this->getValue() .'"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'], '[value]' => $this->getValue())) .' />';

		// template
		if($template !== null)
		{
			$template->assign('txt'. SpoonFilter::toCamelCase($this->attributes['name']), $output);
			$template->assign('txt'. SpoonFilter::toCamelCase($this->attributes['name']) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		return $output;
	}
}


/**
 * Create an html password field
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonPasswordField extends SpoonInputField
{
	/**
	 * Is the content of this field html?
	 *
	 * @var	bool
	 */
	private $isHTML = false;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	int[optional] $maxlength
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $HTML
	 */
	public function __construct($name, $value = null, $maxlength = null, $class = 'inputPassword', $classError = 'inputPasswordError', $HTML = false)
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;

		// custom optional fields
		if($value !== null) $this->setValue($value);
		if($maxlength !== null) $this->attributes['maxlength'] = (int) $maxlength;
		$this->attributes['class'] = (string) $class;
		$this->classError = (string) $classError;
		$this->isHTML = (bool) $HTML;
	}


	/**
	 * Retrieve the initial or submitted value.
	 *
	 * @return	string
	 * @param	bool[optional] $allowHTML
	 */
	public function getValue($allowHTML = null)
	{
		// redefine html & default value
		$allowHTML = ($allowHTML !== null) ? (bool) $allowHTML : $this->isHTML;
		$value = $this->value;

		// contains html
		if($this->isHTML)
		{
			// set value
			$value = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($value) : SpoonFilter::htmlentities($value);
		}

		// form submitted
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// submitted by post (may be empty)
			if(isset($data[$this->getName()]))
			{
				// value
				$value = $data[$this->attributes['name']];

				// maximum length?
				if(isset($this->attributes['maxlength']) && $this->attributes['maxlength'] > 0) $value = mb_substr($value, 0, (int) $this->attributes['maxlength'], SPOON_CHARSET);

				// html allowed?
				if(!$allowHTML) $value = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($value) : SpoonFilter::htmlentities($value);
			}
		}

		return $value;
	}


	/**
	 * Checks if this field contains only letters a-z and A-Z.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isAlphabetical($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isAlphabetical($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field only contains letters & numbers (without spaces).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isAlphaNumeric($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isAlphaNumeric($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field was submitted & filled.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!(isset($data[$this->attributes['name']]) && trim($data[$this->attributes['name']]) != ''))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field's length is less (or equal) than the given maximum.
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param	string[optional] $error
	 */
	public function isMaximumCharacters($maximum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMaximumCharacters($maximum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field's length is more (or equal) than the given minimum.
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	string[optional] $error
	 */
	public function isMinimumCharacters($minimum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMinimumCharacters($minimum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if the field validates against the regexp.
	 *
	 * @return	bool
	 * @param	string $regexp
	 * @param	string[optional] $error
	 */
	public function isValidAgainstRegexp($regexp, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isValidAgainstRegexp($regexp, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Parses the html for this textfield.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a password field. Please provide a name.');

		// start html generation
		$output = '<input type="password" value="'. $this->getValue() .'"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'], '[value]' => $this->getValue())) .' />';

		// template
		if($template !== null)
		{
			$template->assign('txt'. SpoonFilter::toCamelCase($this->attributes['name']), $output);
			$template->assign('txt'. SpoonFilter::toCamelCase($this->attributes['name']) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		return $output;
	}


	/**
	 * Set the initial value.
	 *
	 * @return	void
	 * @param	string $value
	 */
	private function setValue($value)
	{
		$this->value = (string) $value;
	}
}


/**
 * Create an html textarea
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonTextArea extends SpoonInputField
{
	/**
	 * Is html allowed?
	 *
	 * @var	bool
	 */
	private $isHTML = false;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $HTML
	 */
	public function __construct($name, $value = null, $class = 'inputTextarea', $classError = 'inputTextareaError', $HTML = false)
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;

		// custom optional fields
		if($value !== null) $this->setValue($value);
		$this->attributes['cols'] = 62;
		$this->attributes['rows'] = 5;
		$this->attributes['class'] = $class;
		$this->classError = (string) $classError;
		$this->isHTML = (bool) $HTML;
	}


	/**
	 * Retrieve the initial or submitted value.
	 *
	 * @return	string
	 * @param	bool[optional] $allowHTML
	 */
	public function getValue($allowHTML = null)
	{
		// redefine html & default value
		$allowHTML = ($allowHTML !== null) ? (bool) $allowHTML : $this->isHTML;
		$value = $this->value;

		// contains html
		if($this->isHTML)
		{
			// set value
			$value = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($value) : SpoonFilter::htmlentities($value);
		}

		// form submitted
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// submitted by post (may be empty)
			if(isset($data[$this->getName()]))
			{
				// value
				$value = $data[$this->attributes['name']];

				// maximum length?
				if(isset($this->attributes['maxlength']) && $this->attributes['maxlength'] > 0) $value = mb_substr($value, 0, (int) $this->attributes['maxlength'], SPOON_CHARSET);

				// html allowed?
				if(!$allowHTML) $value = (SPOON_CHARSET == 'utf-8') ? SpoonFilter::htmlspecialchars($value) : SpoonFilter::htmlentities($value);
			}
		}

		return $value;
	}


	/**
	 * Checks if this field contains only letters a-z and A-Z.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isAlphabetical($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isAlphabetical($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field only contains letters & numbers (without spaces).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isAlphaNumeric($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isAlphaNumeric($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field was submitted & filled.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!(isset($data[$this->getName()]) && trim($data[$this->attributes['name']]) != ''))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field's length is less than or equal to the given maximum.
	 *
	 * @return	bool
	 * @param	int $maximum
	 * @param	string[optional] $error
	 */
	public function isMaximumCharacters($maximum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMaximumCharacters($maximum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field's length is more than or equal to the given minimum.
	 *
	 * @return	bool
	 * @param	int $minimum
	 * @param	string[optional] $error
	 */
	public function isMinimumCharacters($minimum, $error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isMinimumCharacters($minimum, $data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Checks if this field contains any string that doesn't have control characters (ASCII 0 - 31) but spaces are allowed.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isString($error = null)
	{
		// filled
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// validate
			if(!isset($data[$this->attributes['name']]) || !SpoonFilter::isString($data[$this->attributes['name']]))
			{
				if($error !== null) $this->setError($error);
				return false;
			}

			return true;
		}

		// not submitted
		if($error !== null) $this->setError($error);
		return false;
	}


	/**
	 * Parses the html for this textarea.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is requird for a textarea. Please provide a valid name.');

		// start html generation
		$output = '<textarea';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'], '[value]' => $this->getValue()));

		// close first tag
		$output .= '>';

		// add value
		$output .= $this->getValue();

		// end tag
		$output .= '</textarea>';

		// template
		if($template !== null)
		{
			$template->assign('txt'. SpoonFilter::toCamelCase($this->attributes['name']), $output);
			$template->assign('txt'. SpoonFilter::toCamelCase($this->attributes['name']) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		return $output;
	}


	/**
	 * Set the initial value.
	 *
	 * @return	void
	 * @param	string $value
	 */
	private function setValue($value)
	{
		$this->value = (string) $value;
	}
}


/**
 * Creates an html time field
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonTimeField extends SpoonInputField
{
	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, $value = null, $class = 'inputTimefield', $classError = 'inputTimefieldError')
	{
		// obligated fields
		$this->attributes['id'] = SpoonFilter::toCamelCase($name, '_', true);
		$this->attributes['name'] = (string) $name;

		/**
		 * If no value has presented, the current time
		 * will be used.
		 */
		if($value !== null) $this->setValue($value);
		else $this->setValue(date('H:i'));

		// custom optional fields
		$this->attributes['maxlength'] = 5;
		$this->attributes['class'] = (string) $class;
		$this->classError = (string) $classError;

		// update reserved attributes
		$this->reservedAttributes[] = 'maxlength';
	}


	/**
	 * Returns a timestamp based on the value & optional fields.
	 *
	 * @return	int
	 * @param	int[optional] $year
	 * @param	int[optional] $month
	 * @param	int[optional] $day
	 */
	public function getTimestamp($year = null, $month = null, $day = null)
	{
		// field has been filled in
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// valid field
			if($this->isValid())
			{
				// fetch time
				$hour = (int) substr($this->getValue(), 0, 2);
				$minute = (int) substr($this->getValue(), 3, 2);

				// init vars
				$year = ($year !== null) ? (int) $year : (int) date('Y');
				$month = ($month !== null) ? (int) $month : (int) date('n');
				$day = ($day !== null) ? (int) $day : (int) date('j');

				// create timestamp
				return mktime($hour, $minute, 0, $month, $day, $year);
			}
		}

		// nothing submitted
		return false;
	}


	/**
	 * Retrieve the initial or submitted value.
	 *
	 * @return	string
	 */
	public function getValue()
	{
		// redefine default value
		$value = $this->value;

		// added to form
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// submitted by post (may be empty)
			if(isset($data[$this->attributes['name']]))
			{
				// value
				$value = $data[$this->attributes['name']];
			}
		}

		return $value;
	}


	/**
	 * Checks if this field has any content (except spaces).
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!(isset($data[$this->attributes['name']]) && trim($data[$this->attributes['name']]) != ''))
		{
			if($error !== null) $this->setError($error);
			return false;
		}

		return true;
	}


	/**
	 * Checks if this field is correctly submitted.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isValid($error = null)
	{
		// field has been filled in
		if($this->isFilled())
		{
			// post/get data
			$data = $this->getMethod(true);

			// new time
			$time = '';

			// allowed characters
			$aCharacters = array(':', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

			// replace every character if it's not in the list!
			for($i = 0; $i < strlen($data[$this->attributes['name']]); $i++)
			{
				if(in_array(substr($data[$this->attributes['name']], $i, 1), $aCharacters)) $time .= substr($data[$this->attributes['name']], $i, 1);
			}

			// maxlength checks out (needs to be equal)
			if(strlen($time) == 5 && strpos($time, ':') !== false)
			{
				// define hour & minutes
				$hour = (int) substr($time, 0, 2);
				$minutes = (int) substr($time, 3, 2);

				// validates
				if($hour >= 0 && $hour <= 23 && $minutes >= 0 && $minutes <= 59) return true;
			}
		}

		/**
		 * If the script reaches this point, that means this field has not
		 * been successfully submitted, which results in returning a false
		 * and optionally defining an error.
		 */

		// error defined
		if($error !== null) $this->setError($error);

		// return status
		return false;
	}


	/**
	 * Parses the html for this time field.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a time field. Please provide a name.');

		// start html generation
		$output = '<input type="text" value="'. $this->getValue() .'"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'], '[value]' => $this->getValue())) .' />';

		// template
		if($template !== null)
		{
			$template->assign('txt'. SpoonFilter::toCamelCase($this->attributes['name']), $output);
			$template->assign('txt'. SpoonFilter::toCamelCase($this->attributes['name']) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		return $output;
	}


	/**
	 * Set the value attribute for this time field.
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setValue($value)
	{
		$this->value = (string) $value;
	}
}


/**
 * Generates a single or multiple dropdown menu.
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonDropDown extends SpoonFormAttributes
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
	 * @return	void
	 * @param	string $name
	 * @param	array $values
	 * @param	mixed[optional] $selected
	 * @param	bool[optional] $multipleSelection
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function __construct($name, array $values, $selected = null, $multipleSelection = false, $class = 'inputDropdown', $classError = 'inputDropdownError')
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
		$this->attributes['size'] = 1;
	}


	/**
	 * Adds an error to the error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function addError($error)
	{
		$this->errors .= (string) $error;
	}


	/**
	 * Retrieves the custom attributes as HTML.
	 *
	 * @return	string
	 * @param	array $variables
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
			elseif($key == 'name' && !$this->single) $html .= ' name[]="'. $value .'"';

			// other elements
			else $html .= ' '. $key .'="'. str_replace(array_keys($variables), array_values($variables), $value) .'"';
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
			if($this->attributes['class'] != '' && $this->classError != '') $value = ' class="'. $this->attributes['class'] .' '. $this->classError .'"';

			// only class defined
			elseif($this->attributes['class'] != '') $value = ' class="'. $this->attributes['class'] .'"';

			// only error defined
			elseif($this->classError != '') $value = ' class="'. $this->classError .'"';
		}

		// no errors
		else
		{
			// class defined
			if($this->attributes['class'] != '') $value = ' class="'. $this->attributes['class'] .'"';
		}

		return $value;
	}


	/**
	 * Retrieve the initial value.
	 *
	 * @return	string
	 */
	public function getDefaultValue()
	{
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
	 * @param	string $value
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

		// default values
		$values = $this->values;

		// submitted field
		if($this->isSubmitted() && isset($data[$this->attributes['name']]))
		{
			// option groups
			if($this->optionGroups) $values = $data[$this->attributes['name']];

			// no option groups
			else
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
							if(isset($this->values[$value]) && !in_array($value, $values)) $values[] = $value;
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
						if(isset($this->values[(string) $data[$this->attributes['name']]])) $values = (string) $data[$this->attributes['name']];
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
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// default error
		$hasError = false;

		// value not submitted
		if(!isset($data[$this->attributes['name']])) $hasError = true;

		// value submitted
		else
		{
			// multiple
			if(!$this->single)
			{
				// has to be an array with at least one item in it
				if(is_array($data[$this->attributes['name']]) && count($data[$this->attributes['name']]) != 0) $hasError = false;
				else $hasError = true;
			}

			// single
			else
			{
				// empty value
				if(trim((string) $data[$this->attributes['name']]) == '') $hasError = true;
			}
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
	 * Parses the html for this dropdown.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name is required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for a dropdown menu. Please provide a name.');

		// name?
		if(!$this->single) $this->attributes['name'] .= '[]';

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
				if($this->defaultElement[1] == $this->getSelected() && $this->getSelected() !== null) $output .= ' selected="selected"';
			}

			// end option
			$output .= '>'. $this->defaultElement[0] ."</option>\r\n";
		}

		// has option groups
		if($this->optionGroups)
		{
			foreach($this->values as $groupName => $group)
			{
				// create optgroup
				$output .= "\t" .'<optgroup label="'. $groupName .'">'."\n";

				// loop valuesgoo
				foreach($group as $value => $label)
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

					// add custom attributes
					if(isset($this->optionAttributes[(string) $value]))
					{
						// loop each attribute
						foreach($this->optionAttributes[(string) $value] as $attrKey => $attrValue)
						{
							// add to the output
							$output .= ' '. $attrKey .'="'. $attrValue .'"';
						}
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
			foreach($this->values as $value => $label)
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
					if($this->getSelected() !== null && $value == $this->getSelected()) $output .= ' selected="selected"';
				}

				// add custom attributes
				if(isset($this->optionAttributes[(string) $value]))
				{
					// loop each attribute
					foreach($this->optionAttributes[(string) $value] as $attrKey => $attrValue)
					{
						// add to the output
						$output .= ' '. $attrKey .'="'. $attrValue .'"';
					}
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
			$template->assign('ddm'. SpoonFilter::toCamelCase($this->attributes['name']), $output);
			$template->assign('ddm'. SpoonFilter::toCamelCase($this->attributes['name']) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		return $output;
	}


	/**
	 * Should we allow external data to be added.
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function setAllowExternalData($on = true)
	{
		$this->allowExternalData = (bool) $on;
	}


	/**
	 * Sets the default element (top of the dropdown).
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
	 * Overwrites the error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
	}


	/**
	 * Sets custom option attributes for a specific value.
	 *
	 * @return	void
	 * @param	string $value
	 * @param	array $attributes
	 */
	public function setOptionAttributes($value, array $attributes)
	{
		// set each attribute
		foreach($attributes as $attrKey => $attrValue)
		{
			$this->optionAttributes[(string) $value][(string) $attrKey] = (string) $attrValue;
		}
	}


	/**
	 * Whether you can select one or more items.
	 *
	 * @return	void
	 * @param	bool[optional] $single
	 */
	public function setSingle($single = true)
	{
		$this->single = (bool) $single;
	}


	/**
	 * Set the default selected item(s).
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
	}


	/**
	 * Sets the values for this dropdown menu.
	 *
	 * @return	void
	 * @param	array $values
	 */
	private function setValues(array $values)
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


/**
 * Generates a checkbox.
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonCheckBox extends SpoonFormAttributes
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
	 * @param	bool[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
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
	 * @return	void
	 * @param	string $error
	 */
	public function addError($error)
	{
		$this->errors .= (string) $error;
	}


	/**
	 * Retrieve the attributes as HTML.
	 *
	 * @return	string
	 *.@param	array $variables
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
			else $html .= ' '. $key .'="'. str_replace(array_keys($variables), array_values($variables), $value) .'"';
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
			if($this->attributes['class'] != '' && $this->classError != '') $value = ' class="'. $this->attributes['class'] .' '. $this->classError .'"';

			// only class defined
			elseif($this->attributes['class'] != '') $value = ' class="'. $this->attributes['class'] .'"';

			// only error defined
			elseif($this->classError != '') $value = ' class="'. $this->classError .'"';
		}

		// no errors
		else
		{
			// class defined
			if($this->attributes['class'] != '') $value = ' class="'. $this->attributes['class'] .'"';
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
	 * @param	string[optional] $error
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
	 * @param	string[optional] $error
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
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name required
		if($this->attributes['name'] == '') throw new SpoonFormException('A name is required for checkbox. Please provide a name.');

		// start html generation
		$output = '<input type="checkbox" value="Y"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'])) .' />';

		// template
		if($template !== null)
		{
			$template->assign('chk'. SpoonFilter::toCamelCase($this->attributes['name']), $output);
			$template->assign('chk'. SpoonFilter::toCamelCase($this->attributes['name']) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		return $output;
	}


	/**
	 * Sets the checked status.
	 *
	 * @return	void
	 * @param	bool[optional] $checked
	 */
	public function setChecked($checked = true)
	{
		$this->checked = (bool) $checked;
	}


	/**
	 * Overwrites the error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
	}
}


/**
 * Generates a checkbox.
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		0.1.1
 */
class SpoonMultiCheckBox extends SpoonFormElement
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
	 * @return	void
	 * @param	string $name
	 * @param	array $values
	 * @param	mixed[optional] $checked
	 * @param	string[optional] $class
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
	 * @return	void
	 * @param	string $error
	 */
	public function addError($error)
	{
		$this->errors .= (string) $error;
	}


	/**
	 * Retrieves the custom attributes as HTML.
	 *
	 * @return	string
	 * @param	string $element
	 * @param	array $variables
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
				$html .= ' '. $key .'="'. str_replace(array_keys($variables), array_values($variables), $value) .'"';
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
		// when submitted
		if($this->isSubmitted()) return $this->getValue();

		// default values
		else return $this->checked;
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
	 * Retrieve the value(s).
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
			if(isset($data[$this->name]) && is_array($data[$this->name]))
			{
				// loop values
				foreach($data[$this->name] as $item)
				{
					// external data is allowed
					if($this->allowExternalData) $aValues[] = $item;

					// external data is NOT allowed
					else
					{
						// item exists
						if(isset($this->values[(string) $item])) $aValues[] = $item;
					}
				}
			}
		}

		return $aValues;
	}


	/**
	 * Checks if this field was submitted & contains one more values.
	 *
	 * @return	bool
	 * @param	string[optional] $error
	 */
	public function isFilled($error = null)
	{
		// post/get data
		$data = $this->getMethod(true);

		// value submitted & is an array
		if(isset($data[$this->name]) && is_array($data[$this->attributes['name']]))
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
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// name required
		if($this->name == '') throw new SpoonFormException('A name is required for checkbox. Please provide a name.');

		// loop values
		foreach($this->values as $value => $label)
		{
			// init vars
			$name = 'chk'. SpoonFilter::toCamelCase($this->name);
			$element = array();
			$element[$name] = '<input type="checkbox" name="'. $this->name .'[]" value="'. $value .'"';

			// checked status
			if(in_array($value, $this->getChecked())) $element[$name] .= ' checked="checked"';

			// add attributes
			$element[$name] .= $this->getAttributesHTML($value, array('[id]' => $this->variables[$value]['id'], '[value]' => $value));

			// add variables to this element
			foreach($this->variables[$value] as $variableKey => $variableValue) $element[$variableKey] = $variableValue;

			// end input tag
			$element[$name] .= ' />';

			// add checkbox
			$checkBoxes[] = $element;
		}

		// template
		if($template !== null)
		{
			$template->assign($this->name, $checkBoxes);
			$template->assign('chk'. SpoonFilter::toCamelCase($this->name) .'Error', ($this->errors!= '') ? '<span class="formError">'. $this->errors .'</span>' : '');
		}

		return $checkBoxes;
	}


	/**
	 * Should we allow external data.
	 *
	 * @return	void
	 * @param	bool[optional] $on
	 */
	public function setAllowExternalData($on = true)
	{
		$this->allowExternalData = (bool) $on;
	}


	/**
	 * Sets the checked status.
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
	 * Overwrites the error stack.
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function setError($error)
	{
		$this->errors = (string) $error;
	}


	/**
	 * Set the initial values.
	 *
	 * @return	void
	 * @param	mixed $values
	 * @param	string[optional] $defaultClass
	 */
	private function setValues(array $values, $defaultClass = 'inputCheckbox')
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
				else $this->variables[$value['value']]['id'] = SpoonFilter::toCamelCase($this->name . '_'. $value['value'], '_', true);
			}

			// add some custom vars
			if(!isset($this->variables[$value['value']]['label'])) $this->variables[$value['value']]['label'] = $value['label'];
			if(!isset($this->variables[$value['value']]['value'])) $this->variables[$value['value']]['value'] = $value['value'];

			// add id
			if(!isset($this->attributes[$value['value']]['id'])) $this->attributes[$value['value']]['id'] = $this->variables[$value['value']]['id'];
		}
	}
}


/**
 * Creates an html hidden field
 *
 * @package		html
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.be>
 * @since		1.0.0
 */
class SpoonHiddenField extends SpoonFormAttributes
{
	/**
	 * Value of this hidden field
	 *
	 * @var	string
	 */
	private $value;


	/**
	 * Class constructor.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 */
	public function __construct($name, $value = null)
	{
		// obligated fields
		$this->attributes['id'] = (string) $name;
		$this->attributes['name'] = (string) $name;

		// value
		if($value !== null) $this->value = (string) $value;
	}


	/**
	 * Retrieve the initial or submitted value.
	 *
	 * @return	string
	 */
	public function getValue()
	{
		// redefine default value
		$value = $this->value;

		// added to form
		if($this->isSubmitted())
		{
			// post/get data
			$data = $this->getMethod(true);

			// submitted by post/get (may be empty)
			if(isset($data[$this->attributes['name']]))
			{
				// value
				$value = (string) $data[$this->attributes['name']];
			}
		}

		return $value;
	}


	/**
	 * Checks if this field has any content (except spaces).
	 *
	 * @return	bool
	 */
	public function isFilled()
	{
		// post/get data
		$data = $this->getMethod(true);

		// validate
		if(!(isset($data[$this->attributes['name']]) && trim($data[$this->attributes['name']]) != '')) return false;
		return true;
	}


	/**
	 * Parses the html for this hidden field.
	 *
	 * @return	string
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// start html generation
		$output = '<input type="hidden" value="'. $this->getValue() .'"';

		// add attributes
		$output .= $this->getAttributesHTML(array('[id]' => $this->attributes['id'], '[name]' => $this->attributes['name'], '[value]' => $this->getValue())) .' />';

		// parse hidden field
		if($template !== null) $template->assign('hid'. SpoonFilter::toCamelCase($this->attributes['name']), $output);

		return $output;
	}


	/**
	 * Set the value attribute for this hidden field.
	 *
	 * @return	void
	 * @param	string $value
	 */
	private function setValue($value)
	{
		$this->value = (string) $value;
	}
}

?>