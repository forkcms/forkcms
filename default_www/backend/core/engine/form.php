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
	private $url;


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
		$this->url = Spoon::getObjectReference('url');
		$this->header = Spoon::getObjectReference('header');

		// build a name if there wasn't one provided
		$name = ($name === null) ? SpoonFilter::toCamelCase($this->url->getModule() .'_'. $this->url->getAction(), '_', true) : (string) $name;

		// build the action if it wasn't provided
		$action = ($action === null) ? '/'. $this->url->getQueryString() : (string) $action;

		// call the real form-class
		parent::__construct($name, $action, $method);
	}


	public function addDateField($name, $value = null, $type = null, $date = null, $date2 = null, $class = 'inputDatefield', $classError = 'inputDatefieldError')
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (int) $value : null;
		$type = SpoonFilter::getValue($type, array('from', 'till', 'range'), 'none');
		$date = ($date !== null) ? (int) $date : null;
		$date2 = ($date2 !== null) ? (int) $date2 : null;
		$class = (string) $class;
		$classError = (string) $classError;

		// validate
		if($type == 'from' && ($date == 0 || $date == null)) throw new BackendException('A datefield with type "from" should have a valid date-parameter.');
		if($type == 'till' && ($date == 0 || $date == null)) throw new BackendException('A datefield with type "till" should have a valid date-parameter.');
		if($type == 'range' && ($date == 0 || $date2 == 0 || $date == null || $date2 == null)) throw new BackendException('A datefield with type "range" should have 2 valid date-parameters.');

		// @todo	get prefered mask
		$mask = 'd/m/Y';

		// @todo	get firstday
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
	public function addEditorField($name, $value = null, $class = 'inputEditor', $classError = 'inputEditorError', $HTML = true)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$class = (string) $class;
		$classError = (string) $classError;
		$HTML = (bool) $HTML;

		// we add JS because we need TinyMCE
		$this->header->addJS('tiny_mce/tiny_mce.js', 'core');
		$this->header->addJS('tiny_mce/config.js', 'core', true);

		// add the field
		return $this->addTextArea($name, $value, $class, $classError, $HTML);
	}


	/**
	 * Fetches all the values for this form as key/value pairs
	 *
	 * @return	array
	 * @param	array[optional] $excluded
	 */
	public function getValues(array $excluded = array('form', 'submit'))
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