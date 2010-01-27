<?php

/**
 * FrontendAJAX
 *
 * This class will handle AJAX-related stuff
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author		Davy Hellemans <davy@netlash.com>
 * @since		2.0
 */
class FrontendAJAX
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
		// set the module
		$this->setModule(SpoonFilter::getGetValue('module', null, ''));

		// set the action
		$this->setAction(SpoonFilter::getGetValue('action', null, ''));

		// set the language
		$this->setLanguage(SpoonFilter::getGetValue('language', null, ''));

		// create a new action
		$action = new FrontendAJAXAction($this->getAction(), $this->getModule());

		try
		{
			// execute the action
			$action->execute();
		}

		// we should catch exceptions
		catch(Exception $e)
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(403);

			// if we are debugging we should see the exceptions
			if(SPOON_DEBUG) throw $e;

			// output
			$fakeAction = new FrontendBaseAJAXAction('', '');
			$fakeAction->output(FrontendBaseAJAXAction::ERROR, null, $e->getMessage());
		}
	}


	/**
	 * Get action
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
	 * @param	string $value
	 */
	public function setAction($value)
	{
		$this->action = (string) $value;
	}


	/**
	 * Set the language
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setLanguage($value)
	{
		// get the possible languages
		$possibleLanguages = FrontendLanguage::getActiveLanguages();

		// validate
		if(!in_array($value, array_keys($possibleLanguages)))
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(403);

			// output
			$fakeAction = new FrontendBaseAJAXAction('', '');
			$fakeAction->output(FrontendBaseAJAXAction::FORBIDDEN, null, 'Languages not provided.');
		}
	}


	/**
	 * Set module
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setModule($value)
	{
		// set property
		$this->module = (string) $value;
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
		if(!FrontendAuthentication::isLoggedIn())
		{
			// set the correct header
			SpoonHTTP::setHeadersByCode(403);

			// output
			$fakeAction = new FrontendBaseAJAXAction('', '');
			$fakeAction->output(FrontendBaseAJAXAction::FORBIDDEN, null, 'Not logged in.');
		}

		// set interface language
		FrontendLanguage::setLocale(FrontendAuthentication::getUser()->getSetting('interface_language'));
	}
}

?>