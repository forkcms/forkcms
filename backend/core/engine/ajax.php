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
 * @author Dave Lens <dave.lens@wijs.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class BackendAJAX extends BackendBaseObject implements ApplicationInterface
{
	/**
	 * @var BackendAJAXAction
	 */
	private $ajaxAction;

	/**
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function display()
	{
		return $this->ajaxAction->execute();
	}

	public function initialize()
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

		try
		{
			// create URL instance, since the template modifiers need this object
			$URL = new BackendURL();
			$URL->setModule($module);

			$this->setModule($module);
			$this->setAction($action);
			$this->setLanguage($language);

			// create a new action
			$this->ajaxAction = new BackendAJAXAction($this->getKernel());
			$this->ajaxAction->setModule($this->getModule());
			$this->ajaxAction->setAction($this->getAction());
		}

		catch(Exception $e)
		{
			$this->ajaxAction = new BackendBaseAJAXAction($this->getKernel());
			$this->ajaxAction->output(BackendBaseAJAXAction::ERROR, null, $e->getMessage());
		}
	}

	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		// get the possible languages
		$possibleLanguages = BackendLanguage::getWorkingLanguages();

		// validate
		if(!in_array($language, array_keys($possibleLanguages)))
		{
			throw new BackendException('Language invalid.');
		}

		// set working language
		BackendLanguage::setWorkingLanguage($language);
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
			throw new BackendException('Not logged in.');
		}

		// set interface language
		BackendLanguage::setLocale(BackendAuthentication::getUser()->getSetting('interface_language'));
	}
}
