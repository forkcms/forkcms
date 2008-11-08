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
	 * Holds the error class form faulty fields
	 *
	 * @var	string
	 */
	private $errorClass = 'form-error';


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
	 * Parse status
	 *
	 * @var	bool
	 */
	private $parsed = false;


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
	 * @param	string[optional] $object2
	 * @param	string[optional] $object3
	 */
	public function add($object = null, $object2 = null, $object3 = null)
	{
		// more than one argument
		if(func_num_args() != 0)
		{
			// iterate arguments
			foreach (func_get_args() as $argument)
			{
				// array of objects
				if(is_array($argument)) foreach($argument as $object) $this->add($object);
				
				// object
				else 
				{
					// not an object
					if(!is_object($argument)) throw new SpoonFormException('The provided argument ('. $argument .') is not an object.');
					
					// valid object
					$this->addObject($argument);
				}
			}
		}
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
	 * Adds an object to the main stack
	 *
	 * @return	void
	 * @param	object $object
	 */
	private function addObject($object)
	{
		// type of element
		$elementType = str_replace('spoon', '', strtolower(get_class($object)));

		// not an allowed instance
		if(!is_subclass_of($object, 'SpoonFormElement')) throw new SpoonFormException('The class "'. get_class($object) .'" is not among the classes that can be added to a form.');

		// iterate objects
		foreach($this->objects as $presentObject)
		{
			// no name has been defined!
			if(trim($object->getName()) == '') throw new SpoonFormException('A name has not been defined for this object.');

			// object already added
			if($object->getName() == $presentObject->getName())
			{
				// has already been added
				$alreadyAdded = true;

				// checkbox or radiobutton type
				if($elementType == 'checkbox' || $elementType == 'radiobutton')
				{
					// value of the present & new object differ
					if($object->getDefaultValue() != $presentObject->getDefaultValue()) $alreadyAdded = false;
				}

				// alreaddy added!
				if($alreadyAdded) throw new SpoonFormException('The object with the name "'. $object->getName() .'" has already been added to this form "'. $this->getName() .'"');
			}
		}

		// add to stack & define form
		$this->objects[] = $object;
		$object->setFormName($this->name);
		$object->setMethod($this->method);
		
		// automagically add enctype if needed & not added
		if($elementType == 'filefield' && !isset($this->parameters['enctype'])) $this->setParameter('enctype', 'multipart/form-data');
	}


	/**
	 * Loop all the fields and remove the ones that dont need to be in the form
	 *
	 * @return	void
	 */
	public function cleanupFields()
	{
		// create list of fields
		foreach ($this->objects as $object)
		{
			// file field should not be added since they are kept within the $_FILES
			if(strtolower(get_class($object)) != 'spoonfilefield') $this->fields[] = $object->getName();
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
			foreach ($_POST as $key => $value) if(!in_array($key, $this->fields)) unset($_POST[$key]);

			// create needed keys
			foreach ($this->fields as $field) if(!isset($_POST[$field])) $_POST[$field] = '';
		}

		// get method
		else
		{
			// delete unwanted keys
			foreach ($_GET as $key => $value) if(!in_array($key, $this->fields)) unset($_GET[$key]);

			// create needed keys
			foreach ($this->fields as $field) if(!isset($_GET[$field])) $_GET[$field] = '';
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
	 * Returns the form's status
	 *
	 * @return	bool
	 */
	public function getCorrect()
	{
		// not parsed
		if(!$this->parsed) $this->parse();

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
	private function getParametersAsHtml()
	{
		// start html
		$html = '';
		
		// build & return html
		foreach($this->parameters as $key => $value) $html .= ' '. $key .'="'. $value .'"';
		return $html;
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
     * Loops all the added objects in the added number and sets the errors
     *
     * @return	void
     */
    private function parse()
    {
        // not parsed
        if(!$this->parsed)
        {
        	// define errors
        	$errors = '';

            // has objects
            if(count($this->objects) != 0)
            {
                // loop objecjts
                foreach ($this->objects as $oElement)
                {
                	// check, since some objects don't have this method!
                	if(method_exists($oElement, 'getErrors')) $errors .= $oElement->getErrors();
                }

                // affect correct status
                if(trim($errors) != '') $this->correct = false;
            }

            // main form errors?
            if(trim($this->getErrors()) != '') $this->correct = false;

            // update parsed status
            $this->parsed = true;
        }
    }


	/**
	 * Automatically parses the added form elements in the given template
	 *
	 * @return	void
	 * @param	SpoonTemplate $template
	 */
	public function parseElements(SpoonTemplate $template)
	{
		// elements have been added
		if(count($this->objects) != 0)
		{
			// loop objects
			foreach ($this->objects as $object)
			{
				// type of element
				$class = str_replace('spoon', '', strtolower(get_class($object)));

				// convert "one_two_three" into "OneTwoThree"
				$name = SpoonFilter::toCamelCase($object->getName());
				
				// custom shizzle
				switch($class)
				{
					// buttons
					case 'button':
					case 'resetbutton':
					case 'submitbutton':
						// parse field
						$template->assign('btn'. $name, $object->getHtml());
					break;
					
					// checkbox
					case 'checkbox':
						/**
						 * For this element, errors are parsed before assigning the field
						 * itself, because a multiple checkbox series only can have one error total.
						 * eg 5 checkboxes asking about your hobbies will have only 1 error if at least
						 * one item has to be checked.
						 */
						if($object->getErrors() != '') $template->assign('chk'. $name .'Error', '<span class="'. $this->errorClass .'">'. $object->getErrors() .'</span>');
						else $template->assign('chk'. $name .'Error', '');
	
						// add value to the name
						if(!$object->getSingle()) $name .= SpoonFilter::toCamelCase($object->getDefaultValue());
	
						// parse field
						$template->assign('chk'. $name, $object->getHtml());
					break;
					
					// dropdown
					case 'dropdown':
						// parse field
						$template->assign('ddm'. $name, $object->getHtml());
	
						// parse error
						if($object->getErrors() != '') $template->assign('ddm'. $name .'Error', '<span class="'. $this->errorClass .'">'. $object->getErrors() .'</span>');
						else $template->assign('ddm'. $name .'Error', '');
					break;
					
					// filefields
					case 'filefield':
						// parse field
						$template->assign('file'. $name, $object->getHtml());
						
						// parse error
						if($object->getErrors() != '') $template->assign('file'. $name .'Error', '<span class="'. $this->errorClass .'">'. $object->getErrors() .'</span>');
						else $template->assign('file'. $name .'Error', '');
					break;
					
					// hidden fields
					case 'hiddenfield':
						$template->assign('hid'. $name, $object->getHtml());
					break;
					
					
					case 'radiobutton':
						// update name
						$name .= SpoonFilter::toCamelCase($object->getDefaultValue());
	
						// parse field
						$template->assign('rbt'. $name, $object->getHtml());
	
						// parse error
						if($object->getErrors() != '') $template->assign('rbt'. $name .'Error', '<span class="'. $this->errorClass .'">'. $object->getErrors() .'</span>');
						else $template->assign('rbt'. $name .'Error', '');
					break;
					
					// textfields
					case 'datefield':
					case 'passwordfield':
					case 'textarea':
					case 'textfield':
					case 'timefield':
						// parse field
						$template->assign('txt'. $name, $object->getHtml());
						
						// parse error
						if($object->getErrors() != '') $template->assign('txt'. $name .'Error', '<span class="'. $this->errorClass .'">'. $object->getErrors() .'</span>');
						else $template->assign('txt'. $name .'Error', '');
					break;
				}
			}
			
			// parse form tag
			$this->parseFormTags($template);
		}
	}
	
	
	/**
	 * Parse the form tags in the given template
	 * 
	 * @return	void
	 * @param	SpoonTemplate $template
	 */
	private function parseFormTags(SpoonTemplate $template)
	{
		// parse form tag & hidden field
		$aSearch = array('{form:'. $this->name .'}', '{/form:'. $this->name .'}');
		$aReplace = array('<form action="'. $this->action .'" method="'. $this->method .'"'. $this->getParametersAsHtml() .">\n<fieldset>". $this->objects[0]->getHtml(), '</fieldset></form>');
		$template->assignHighlight($aSearch, $aReplace);
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
	 * Set the error class for elements that have errors
	 *
	 * @return	void
	 * @param	string $name
	 */
	public function setErrorClass($name)
	{
		$this->errorClass = (string) $name;
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
		// already parsed
		if($this->parsed) throw new SpoonFormException('This form ('. $this->name .') has already been parsed.');

		// not parsed yet
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
		if(!$this->parsed) $this->parse();
	}
}

?>