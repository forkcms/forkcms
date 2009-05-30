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
		$this->addTextArea($name, $value, $class, $classError, $HTML);
	}


	/**
	 * Fetches all the values for this form as key/value pairs
	 *
	 * @return	array
	 */
	public function getValues($aIgnoreKeys = array('form', 'submit'))
	{
		return parent::getValues($aIgnoreKeys);
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
		if($this->isSubmitted() && !$this->getCorrect()) $tpl->assign('formError', true);
	}
}

?>