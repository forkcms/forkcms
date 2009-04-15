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


/** Spoon class */
require_once 'spoon/spoon.php';

/** SpoonFormException class */
require_once 'spoon/html/form/exception.php';

/** SpoonElement class */
require_once 'spoon/html/form/element.php';

/** SpoonVisualElement class */
require_once 'spoon/html/form/visual_element.php';

/** SpoonInputField class */
require_once 'spoon/html/form/input_field.php';

/** SpoonButton class */
require_once 'spoon/html/form/button.php';

/** SpoonFileField class */
require_once 'spoon/html/form/filefield.php';

/** SpoonRadioButton class */
require_once 'spoon/html/form/radiobutton.php';

/** SpoonDateField class */
require_once 'spoon/html/form/datefield.php';

/** SpoonTextField class */
require_once 'spoon/html/form/textfield.php';

/** SpoonPasswordField class */
require_once 'spoon/html/form/passwordfield.php';

/** SpoonTextArea class */
require_once 'spoon/html/form/textarea.php';

/** SpoonTimeField class */
require_once 'spoon/html/form/timefield.php';

/** SpoonDropDown class */
require_once 'spoon/html/form/dropdown.php';

/** SpoonCheckBox class */
require_once 'spoon/html/form/checkbox.php';

/** SpoonMultiCheckBox class */
require_once 'spoon/html/form/multi_checkbox.php';

/** SpoonHiddenField class */
require_once 'spoon/html/form/hiddenfield.php';



/**
 * The class that handles the forms
 *
 * @package			html
 * @subpackage		form
 *
 *
 * @author			Davy Hellemans <davy@spoon-library.be>
 * @since			0.1.1
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
	 * Add one or more objects to the stack
	 *
	 * @return	void
	 * @param	string[optional] $object
	 * @param	string[optional] $objectTwo
	 * @param	string[optional] $objectThree
	 */
	public function add($object = null, $objectTwo = null, $objectThree = null)
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
					if(!is_object($argument)) throw new SpoonFormException('The provided argument ('. $argument .') is not an object.');

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
	 * Adds a single button
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string $value
	 * @param	string[optional] $type
	 * @param	string[optional] $class
	 */
	public function addButton($name, $value, $type = null, $class = 'inputButton')
	{
		$this->add(new SpoonButton($name, $value, $type, $class));
	}


	/**
	 * Adds a single checkbox
	 *
	 * @return	void
	 * @param	string $name
	 * @param	bool[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addCheckBox($name, $checked = false, $class = 'inputCheckbox', $classError = 'inputCheckboxError')
	{
		$this->add(new SpoonCheckBox($name, $checked, $class, $classError));
	}


	/**
	 * Adds one or more checkboxes
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
	 * Adds a single datefield
	 *
	 * @return	void
	 * @param	string $name
	 * @param	int[optional] $name
	 * @param	string[optional] $mask
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addDateField($name, $value = null, $mask = null, $class = 'inputDatefield', $classError = 'inputDatefieldError')
	{
		$this->add(new SpoonDateField($name, $value, $mask, $class, $classError));
	}


	/**
	 * Adds a single dropdown
	 *
	 * @return	void
	 * @param	string $name
	 * @param	array $values
	 * @param	string[optional] $selected
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addDropDown($name, array $values, $selected = null, $class = 'inputDropdown', $classError = 'inputDropdownError')
	{
		$this->add(new SpoonDropDown($name, $values, $selected, $class, $classError));
	}


	/**
	 * Adds an error to the main error stack
	 *
	 * @return	void
	 * @param	string $error
	 */
	public function addError($error)
	{
		$this->errors .= trim((string) $error);
	}


	/**
	 * Adds a single file field
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addFileField($name, $class = 'inputFilefield', $classError = 'inputFilefieldError')
	{
		$file = new SpoonFileField($name, $class, $classError);
	}


	/**
	 * Adds one or more file fields
	 *
	 * @return	void
	 */
	public function addFileFields()
	{
		foreach(func_get_args() as $argument) $this->add(new SpoonFileField((string) $argument));
	}


	/**
	 * Adds a single hidden field
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 */
	public function addHiddenField($name, $value = null)
	{
		$this->add(new SpoonHiddenField($name, $value));
	}


	/**
	 * Adds one or more hidden fields
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
	 * Adds a single multiple checkbox
	 *
	 * @return	void
	 * @param	string $name
	 * @param	bool[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addMultiCheckBox($name, $checked = false, $class = 'inputCheckbox', $classError = 'inputCheckboxError')
	{
		$this->add(new SpoonMultiCheckBox($name, $checked, $class, $classError));
	}


	/**
	 * Adds a single password field
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	int[optional] $maxlength
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $html
	 */
	public function addPasswordField($name, $value = null, $maxlength = null, $class = 'inputPassword', $classError = 'inputPasswordError', $html = false)
	{
		$this->add(new SpoonPasswordField($name, $value, $maxlength, $class, $classError, $html));
	}


	/**
	 * Adds one or more password fields
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
	 * Adds a single radiobutton
	 *
	 * @return	void
	 * @param	string $name
	 * @param	array $values
	 * @param	mixed[optional] $checked
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addRadioButton($name, array $values, $checked = null, $class = 'inputRadiobutton', $classError = 'inputRadiobuttonError')
	{
		$this->add(new SpoonRadioButton($name, $values, $checked, $class, $classError));
	}


	/**
	 * Adds a single textarea
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $html
	 */
	public function addTextArea($name, $value = null, $class = 'inputTextarea', $classError = 'inputTextareaError', $html = false)
	{
		$this->add(new SpoonTextArea($name, $value, $class, $classError, $html));
	}


	/**
	 * Adds one or more textareas
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
	 * Adds a single textfield
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	int[optional] $maxlength
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $html
	 */
	public function addTextField($name, $value = null, $maxlength = null, $class = 'inputTextfield', $classError = 'inputTextfieldError', $html = false)
	{
		$this->add(new SpoonTextField($name, $value, $maxlength, $class, $classError, $html));
	}


	/**
	 * Adds one or more textfields
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
	 * Adds a single timefield
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
	}


	/**
	 * Adds one or more timefields
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
	 * Loop all the fields and remove the ones that dont need to be in the form
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
		if(!in_array('form', $this->fields)) $this->fields[] = 'form'; // default

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
	 * Creates a hidden field & adds it to the form
	 *
	 * @return	void
	 */
	private function createHiddenField()
	{
		$this->add(new SpoonHiddenField('form', $this->name));
	}


	/**
	 * Retrieve the action
	 *
	 * @return	string
	 */
	public function getAction()
	{
		return $this->action;
	}


	/**
	 * Returns the form's status
	 *
	 * @return	bool
	 */
	public function getCorrect()
	{
		// not parsed
		if(!$this->validated) $this->validate();

		// return current status
		return $this->correct;
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
	 * Fetches a field
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
	 * Retrieve all fields
	 *
	 * @return	array
	 */
	public function getFields()
	{
		return $this->objects;
	}


	/**
	 * Retrieve the method post/get
	 *
	 * @return	string
	 */
	public function getMethod()
	{
		return $this->method;
	}


	/**
	 * Retrieve the name of this form
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * Retrieve the parameters
	 *
	 * @return	array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * Retrieve the parameters in html form
	 *
	 * @return	string
	 */
	public function getParametersAsHTML()
	{
		// start html
		$html = '';

		// build & return html
		foreach($this->parameters as $key => $value) $html .= ' '. $key .'="'. $value .'"';
		return $html;
	}


	/**
	 * Generates an example template, based on the elements already added
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
			if($object instanceof SpoonHiddenField)
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
					$value .= "\t". '<label for="'. $object->getId() .'">'. SpoonFilter::toCamelCase($object->getName()) ."</label>\n";
					$value .= "\t<p>\n";
					$value .= "\t\t{\$chk". SpoonFilter::toCamelCase($object->getName()) ."}\n";
					$value .= "\t\t{\$chk". SpoonFilter::toCamelCase($object->getName()) ."Errors}\n";
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
					$value .= "\t\t". '{$chk'. SpoonFilter::toCamelCase($object->getName()) ."Errors}\n";
					$value .= "\t<p>\n";
				}

				// dropdowns
				elseif($object instanceof SpoonDropDown)
				{
					$value .= "\t". '<label for="'. $object->getId() .'">'. SpoonFilter::toCamelCase($object->getName()) ."</label>\n";
					$value .= "\t<p>\n";
					$value .= "\t\t". '{$ddm'. SpoonFilter::toCamelCase($object->getName()) ."}\n";
					$value .= "\t\t". '{$ddm'. SpoonFilter::toCamelCase($object->getName()) ."Errors}\n";
					$value .= "\t</p>\n";
				}

				// filefields
				elseif($object instanceof SpoonFileField)
				{
					$value .= "\t". '<label for="'. $object->getId() .'">'. SpoonFilter::toCamelCase($object->getName()) ."</label>\n";
					$value .= "\t<p>\n";
					$value .= "\t\t". '{$file'. SpoonFilter::toCamelCase($object->getName()) ."}\n";
					$value .= "\t\t". '{$file'. SpoonFilter::toCamelCase($object->getName()) ."Errors}\n";
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
					$value .= "\t\t". '{$rbt'. SpoonFilter::toCamelCase($object->getName()) ."Errors}\n";
					$value .= "\t<p>\n";
				}

				// textfields
				elseif(($object instanceof SpoonDateField) || ($object instanceof SpoonPasswordField) || ($object instanceof SpoonTextArea) || ($object instanceof SpoonTextField) || ($object instanceof SpoonTimeField))
				{
					$value .= "\t". '<label for="'. $object->getId() .'">'. SpoonFilter::toCamelCase($object->getName()) ."</label>\n";
					$value .= "\t<p>\n";
					$value .= "\t\t". '{$txt'. SpoonFilter::toCamelCase($object->getName()) ."}\n";
					$value .= "\t\t". '{$txt'. SpoonFilter::toCamelCase($object->getName()) ."Errors}\n";
					$value .= "\t</p>\n";
				}
			}
		}

		// close form tag
		return $value .'{/form:'. $this->name .'}';
	}


	/**
	 * Fetches all the values for this form as key/value pairs
	 *
	 * @return	array
	 */
	public function getValues()
	{
		// values
		$aValues = array();

		// loop objects
		foreach($this->objects as $object) $aValues[$object->getName()] = $object->getValue();

		// return data
		return $aValues;
	}


	/**
	 * Returns whether this form has been submitted
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
	 * Parse this form in the given template
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
	 * Set the action
	 *
	 * @return	void
	 * @param	string $action
	 */
	public function setAction($action)
	{
		$this->action = (string) $action;
	}


	/**
	 * Sets the correct value
	 *
	 * @return	void
	 * @param	bool[optional] $correct
	 */
	private function setCorrect($correct = true)
	{
		$this->correct = (bool) $correct;
	}


	/**
	 * Set the form method
	 *
	 * @return	void
	 * @param	string[optional] $method
	 */
	public function setMethod($method = 'post')
	{
		$this->method = SpoonFilter::getValue((string) $method, array('get', 'post'), 'post');
	}


	/**
	 * Set the name
	 *
	 * @return	void
	 * @param	string $name
	 */
	private function setName($name)
	{
		$this->name = (string) $name;
	}


	/**
	 * Set a parameter for the form tag
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
	 * Set multiple form parameters
	 *
	 * @return	void
	 * @param	array $parameters
	 */
	public function setParameters(array $parameters)
	{
		foreach($parameters as $key => $value) $this->setParameter($key, $value);
	}


	/**
	 * Validates the form (when not wanting to use getcorrect)
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

?>