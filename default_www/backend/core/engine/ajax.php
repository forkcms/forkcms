<?php

/**
 * BackendAJAX
 *
 * This class will handle AJAX-related stuff
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
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

		// set the module
		$this->setModule((string) SpoonFilter::getGetValue('module', null, ''));

		// set the action
		$this->setAction((string) SpoonFilter::getGetValue('action', null, ''));

		// set the language
		$this->setLanguage((string) SpoonFilter::getGetValue('language', BackendLanguage::getInterfaceLanguages(), BackendLanguage::DEFAULT_LANGUAGE));

		// create a new action
		$action = new BackendAJAXAction($this->getAction(), $this->getModule());

		// execute the action
		$action->execute();
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
		// set property
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
		BackendLanguage::setLocale($value);
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

		// is this module allowed?
		if(!BackendAuthentication::isAllowedModule($this->module))
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(403);

			// throw an exception, when debug is on we get a descent message
			throw new BackendException('Not allowed module.');
		}
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

			// throw an exception, when debug is on we get a descent message
			throw new BackendException('Not logged in.');
		}
	}
}

?>