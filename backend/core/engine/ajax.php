<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will handle AJAX-related stuff
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
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

	public function __construct()
	{
		// check if the user is logged in
		$this->validateLogin();

		// named application
		if(!defined('NAMED_APPLICATION'))
		{
			define('NAMED_APPLICATION', 'backend_ajax');
		}

		// get values from the GET-parameters
		$module = (isset($_GET['fork']['module'])) ? $_GET['fork']['module'] : '';
		$action = (isset($_GET['fork']['action'])) ? $_GET['fork']['action'] : '';
		$language = (isset($_GET['fork']['language'])) ? $_GET['fork']['language'] : SITE_DEFAULT_LANGUAGE;

		// overrule the values with the ones provided through POST
		$module = (isset($_POST['fork']['module'])) ? $_POST['fork']['module'] : $module;
		$action = (isset($_POST['fork']['action'])) ? $_POST['fork']['action'] : $action;
		$language = (isset($_POST['fork']['language'])) ? $_POST['fork']['language'] : $language;

		$this->setModule($module);
		$this->setAction($action);
		$this->setLanguage($language);

		// create a new action
		$action = new BackendAJAXAction($this->getAction(), $this->getModule());

		try
		{
			$action->execute();
		}

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
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * Get module
	 *
	 * @return string
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * Set action
	 *
	 * @param string $value The action to use.
	 */
	public function setAction($value)
	{
		$this->action = (string) $value;
	}

	/**
	 * Set the language
	 *
	 * @param string $value The language to set.
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
	 * @param string $value The module to use.
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

		// create URL instance, since the template modifiers need this object
		$URL = new BackendURL();
		$URL->setModule($this->module);
	}

	/**
	 * Do authentication stuff
	 * This method could end the script by throwing an exception
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
