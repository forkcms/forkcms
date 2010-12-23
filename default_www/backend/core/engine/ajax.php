<?php

/**
 * This class will handle AJAX-related stuff
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class BackendAJAX
{
	/**
	 * The action
	 *
	 * @var	string
	 */
	private $action;


	/**
	 * The module
	 *
	 * @var	string
	 */
	private $module;


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// check if the user is logged in
		$this->validateLogin();

		// named application
		if(!defined('NAMED_APPLICATION')) define('NAMED_APPLICATION', 'backend_ajax');

		// set the module
		$this->setModule(SpoonFilter::getGetValue('module', null, ''));

		// set the action
		$this->setAction(SpoonFilter::getGetValue('action', null, ''));

		// set the language
		$this->setLanguage(SpoonFilter::getGetValue('language', null, ''));

		// create a new action
		$action = new BackendAJAXAction($this->getAction(), $this->getModule());

		// try to execute
		try
		{
			// execute the action
			$action->execute();
		}

		// we should catch exceptions
		catch(Exception $e)
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(500);

			// if we are debugging we should see the exceptions
			if(SPOON_DEBUG) throw $e;

			// output
			$fakeAction = new BackendBaseAJAXAction('', '');
			$fakeAction->output(BackendBaseAJAXAction::ERROR, null, $e->getMessage());
		}
	}


	/**
	 * Get the action
	 *
	 * @return	string
	 */
	public function getAction()
	{
		return $this->action;
	}


	/**
	 * Get module
	 *
	 * @return	string
	 */
	public function getModule()
	{
		return $this->module;
	}


	/**
	 * Set action
	 *
	 * @return	void
	 * @param	string $value	The action to use.
	 */
	public function setAction($value)
	{
		$this->action = (string) $value;
	}


	/**
	 * Set the language
	 *
	 * @return	void
	 * @param	string $value	The language to set.
	 */
	public function setLanguage($value)
	{
		// get the possible languages
		$possibleLanguages = BackendLanguage::getWorkingLanguages();

		// validate
		if(!in_array($value, array_keys($possibleLanguages)))
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(500);

			// output
			$fakeAction = new BackendBaseAJAXAction('', '');
			$fakeAction->output(BackendBaseAJAXAction::FORBIDDEN, null, 'Languages not provided.');
		}

		// set working language
		BackendLanguage::setWorkingLanguage($value);
	}


	/**
	 * Set module
	 *
	 * @return	void
	 * @param	string $value	The module to use.
	 */
	public function setModule($value)
	{
		// set property
		$this->module = (string) $value;

		// is this module allowed?
		if(!BackendAuthentication::isAllowedModule($this->module))
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(403);

			// output
			$fakeAction = new BackendBaseAJAXAction('', '');
			$fakeAction->output(BackendBaseAJAXAction::FORBIDDEN, null, 'Module not allowed.');
		}

		// create URL instance, the templatemodifiers need this object
		$URL = new BackendURL();

		// set the module
		$URL->setModule($this->module);
	}


	/**
	 * Do authentication stuff
	 * This method could end the script by throwing an exception
	 *
	 * @return	void
	 */
	private function validateLogin()
	{
		// check if the user is logged on, if not he shouldn't load any JS-file
		if(!BackendAuthentication::isLoggedIn())
		{
			// set the correct header
			SpoonHTTP::setHeadersByCode(403);

			// output
			$fakeAction = new BackendBaseAJAXAction('', '');
			$fakeAction->output(BackendBaseAJAXAction::FORBIDDEN, null, 'Not logged in.');
		}

		// set interface language
		BackendLanguage::setLocale(BackendAuthentication::getUser()->getSetting('interface_language'));
	}
}

?>