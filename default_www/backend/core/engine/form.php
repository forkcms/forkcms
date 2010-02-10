<?php

/**
 * BackendForm, this is our extended version of SpoonForm
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendForm extends SpoonForm
{
	/**
	 * The header instance
	 *
	 * @var	BackendHeader
	 */
	private $header;


	/**
	 * The URL instance
	 *
	 * @var	BackendURL
	 */
	private $URL;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string[optional] $name
	 * @param	string[optional] $action
	 * @param	string[optional] $method
	 */
	public function __construct($name = null, $action = null, $method = 'post')
	{
		// init the URL-instance
		$this->URL = Spoon::getObjectReference('url');
		$this->header = Spoon::getObjectReference('header');

		// build a name if there wasn't one provided
		$name = ($name === null) ? SpoonFilter::toCamelCase($this->URL->getModule() .'_'. $this->URL->getAction(), '_', true) : (string) $name;

		// build the action if it wasn't provided
		$action = ($action === null) ? '/'. $this->URL->getQueryString() : (string) $action;

		// call the real form-class
		parent::__construct($name, $action, $method);

		// add default classes
		$this->setParameter('id', $name);
		$this->setParameter('class', 'forkForms submitWithLink');
	}


	/**
	 * Adds a button to the form
	 *
	 * @return	SpoonButton
	 * @param	string $name
	 * @param	string $value
	 * @param	string[optional] $type
	 * @param	string[optional] $class
	 */
	public function addButton($name, $value, $type = 'submit', $class = null)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$type = (string) $type;
		$class = ($class !== null) ? (string) $class : 'inputText inputButton';

		// do a check
		if($type == 'submit' && $name == 'submit') throw new BackendException('You can\'t add buttons with the name submit. JS freaks out when we replace the buttons with a link and use that link to submit the form.');

		// call the real form class
		return parent::addButton($name, $value, $type, $class);
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
	public function addCheckBox($name, $checked = false, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$checked = (bool) $checked;
		$class = ($class !== null) ? (string) $class : 'inputCheckbox';
		$classError = ($classError !== null) ? (string) $classError : 'inputCheckboxError';

		// return element
		return parent::addCheckBox($name, $checked, $class, $classError);
	}


	/**
	 * Adds a datefield to the form
	 *
	 * @return	SpoonDateField
	 * @param	string $name
	 * @param	int[optional] $value
	 * @param	string[optional] $type
	 * @param	int[optional] $date
	 * @param	int[optional] $date2
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addDateField($name, $value = null, $type = null, $date = null, $date2 = null, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (int) $value : null;
		$type = SpoonFilter::getValue($type, array('from', 'till', 'range'), 'none');
		$date = ($date !== null) ? (int) $date : null;
		$date2 = ($date2 !== null) ? (int) $date2 : null;
		$class = ($class !== null) ? (string) $class : 'inputText inputDatefield';
		$classError = ($classError !== null) ? (string) $classError : 'inputTextError inputDatefieldError';

		// validate
		if($type == 'from' && ($date == 0 || $date == null)) throw new BackendException('A datefield with type "from" should have a valid date-parameter.');
		if($type == 'till' && ($date == 0 || $date == null)) throw new BackendException('A datefield with type "till" should have a valid date-parameter.');
		if($type == 'range' && ($date == 0 || $date2 == 0 || $date == null || $date2 == null)) throw new BackendException('A datefield with type "range" should have 2 valid date-parameters.');

		// @later	get prefered mask & first day
		$mask = 'd/m/Y';
		$firstday = 1;

		// rebuild mask
		$relMask = str_replace(array('d', 'm', 'Y', 'j', 'n'), array('dd', 'mm', 'yy', 'd', 'm'), $mask);

		// build rel
		$rel = $relMask .':::'. $firstday;

		// add extra classes based on type
		switch($type)
		{
			case 'from':
				$class .= ' inputDatefieldFrom';
				$classError .= ' inputDatefieldFrom';
				$rel .= ':::'. date('Y-m-d', $date);
			break;

			case 'till':
				$class .= ' inputDatefieldTill';
				$classError .= ' inputDatefieldTill';
				$rel .= ':::'. date('Y-m-d', $date);
			break;

			case 'range':
				$class .= ' inputDatefieldRange';
				$classError .= ' inputDatefieldRange';
				$rel .= ':::'. date('Y-m-d', $date) .':::'. date('Y-m-d', $date2);
			break;

			default:
				$class .= ' inputDatefieldNormal';
				$classError .= ' inputDatefieldNormal';
			break;
		}

		// call parent
		parent::addDateField($name, $value, $mask, $class, $classError);

		// set attributes
		parent::getField($name)->setAttributes(array('rel' => $rel));

		// fetch field
		return parent::getField($name);
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
	public function addDropDown($name, array $values, $selected = null, $multipleSelection = false, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$values = (array) $values;
		$selected = ($selected !== null) ? (string) $selected : null;
		$multipleSelection = (bool) $multipleSelection;
		$class = ($class !== null) ? (string) $class : 'inputDropdown';
		$classError = ($classError !== null) ? (string) $classError : 'inputDropdownError';

		// return element
		return parent::addDropDown($name, $values, $selected, $multipleSelection, $class, $classError);
	}


	/**
	 * Add an editor field
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $value
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 * @param	bool[optional] $HTML
	 */
	public function addEditorField($name, $value = null, $class = null, $classError = null, $HTML = true)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$class = 'inputEditor '. (string) $class;
		$classError = 'inputEditorError '. (string) $classError;
		$HTML = (bool) $HTML;

		// we add JS because we need TinyMCE
		$this->header->addJavascript('tiny_mce/tiny_mce.js', 'core');
		$this->header->addJavascript('tiny_mce/config.js', 'core', true);

		// add the field
		return $this->addTextArea($name, $value, $class, $classError, $HTML);
	}


	/**
	 * Adds a single file field.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addFileField($name, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$class = ($class !== null) ? (string) $class : 'inputFilefield';
		$classError = ($classError !== null) ? (string) $classError : 'inputFilefieldError';

		// return element
		return parent::addFileField($name, $class, $classError);
	}


	/**
	 * Adds a single image field.
	 *
	 * @return	void
	 * @param	string $name
	 * @param	string[optional] $class
	 * @param	string[optional] $classError
	 */
	public function addImageField($name, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$class = ($class !== null) ? (string) $class : 'inputFilefield';
		$classError = ($classError !== null) ? (string) $classError : 'inputFilefieldError';

		// return element
		return parent::addImageField($name, $class, $classError);
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
	public function addMultiCheckBox($name, array $values, $checked = null, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$values = (array) $values;
		$checked = ($checked !== null) ? (bool) $checked : null;
		$class = ($class !== null) ? (string) $class : 'inputCheckbox';
		$classError = ($classError !== null) ? (string) $classError : 'inputCheckboxError';

		// return element
		return parent::addMultiCheckBox($name, $values, $checked, $class, $classError);
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
	public function addPasswordField($name, $value = null, $maxlength = null, $class = null, $classError = null, $HTML = false)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$maxlength = ($maxlength !== null) ? (int) $maxlength : null;
		$class = ($class !== null) ? (string) $class : 'inputText inputPassword';
		$classError = ($classError !== null) ? (string) $classError : 'inputTextError inputPasswordError';
		$HTML = (bool) $HTML;

		// return element
		return parent::addPasswordField($name, $value, $maxlength, $class, $classError, $HTML);
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
	public function addRadioButton($name, array $values, $checked = null, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$values = (array) $values;
		$checked = ($checked !== null) ? (string) $checked : null;
		$class = ($class !== null) ? (string) $class : 'inputRadiobutton';
		$classError = ($classError !== null) ? (string) $classError : 'inputRadiobuttonError';

		// return element
		return parent::addRadioButton($name, $values, $checked, $class, $classError);
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
	public function addTextArea($name, $value = null, $class = null, $classError = null, $HTML = false)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$class = ($class !== null) ? (string) $class : 'inputTextarea';
		$classError = ($classError !== null) ? (string) $classError : 'inputTextareaError';
		$HTML = (bool) $HTML;

		// return element
		return parent::addTextArea($name, $value, $class, $classError, $HTML);
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
	public function addTextField($name, $value = null, $maxlength = null, $class = null, $classError = null, $HTML = false)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$maxlength = ($maxlength !== null) ? (int) $maxlength : null;
		$class = ($class !== null) ? (string) $class : 'inputTextfield';
		$classError = ($classError !== null) ? (string) $classError : 'inputTextfieldError';
		$HTML = (bool) $HTML;

		// return element
		return parent::addTextField($name, $value, $maxlength, $class, $classError, $HTML);
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
	public function addTimeField($name, $value = null, $class = null, $classError = null)
	{
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$class = ($class !== null) ? (string) $class : 'inputTimefield';
		$classError = ($classError !== null) ? (string) $classError : 'inputTimefieldError';

		// return element
		return parent::addTimeField($name, $value, $class, $classError);
	}


	/**
	 * Fetches all the values for this form as key/value pairs
	 *
	 * @return	array
	 * @param	mixed[optional] $excluded
	 */
	public function getValues($excluded = array('form', 'save'))
	{
		return parent::getValues($excluded);
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 * @param	SpoonTemplate $tpl
	 */
	public function parse(SpoonTemplate $tpl)
	{
		// parse the form
		parent::parse($tpl);

		// validate the form
		$this->validate();

		// if the form is submitted but there was an error, assign a general error
		if($this->isSubmitted() && !$this->isCorrect()) $tpl->assign('formError', true);
	}
}

?>