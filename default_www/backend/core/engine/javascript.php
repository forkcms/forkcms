<?php

/**
 * BackendJavascript
 *
 * This class will handle files JS-files that have to be parsed by PHP
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendJavascript
{
	/**
	 * The actual filename
	 *
	 * @var	string
	 */
	private $file;


	/**
	 * The working language
	 *
	 * @var	string
	 */
	private $language;


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
		// define the Named appliation
		if(!defined('NAMED_APPLICATION')) define('NAMED_APPLICATION', 'backend');

		// check if the user is logged in
		$this->validateLogin();

		// set the module
		$this->setModule((string) SpoonFilter::getGetValue('module', null, ''));

		// set the requested file
		$this->setFile((string) SpoonFilter::getGetValue('file', null, ''));

		// set the language
		$this->setLanguage((string) SpoonFilter::getGetValue('language', FrontendLanguage::getActiveLanguages(), FrontendLanguage::DEFAULT_LANGUAGE));

		// create a new template instance (this will handle all stuff for us)
		$tpl = new BackendTemplate();

		// output the template
		if($this->module == 'core') $tpl->display(BACKEND_CORE_PATH .'/js/'. $this->getFile());
		else $tpl->display(BACKEND_MODULES_PATH .'/'. $this->getModule() .'/js/'. $this->getFile());
	}


	/**
	 * Get file
	 *
	 * @return	string
	 */
	public function getFile()
	{
		return $this->file;
	}


	/**
	 * Get language
	 *
	 * @return	string
	 */
	public function getLanguage()
	{
		return $this->language;
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
	 * Set file
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setFile($value)
	{
		// set property
		$this->file = (string) $value;

		// core is a special module
		if($this->module == 'core')
		{
			// check if the path exists, if not whe should given an error
			if(!SpoonFile::exists(BACKEND_CORE_PATH .'/js/'. $this->file))
			{
				// set correct headers
				SpoonHTTP::setHeadersByCode(404);

				// throw an exception, when debug is on we get a descent message
				throw new BackendException('File not present.');
			}
		}

		// not core
		else
		{
			// check if the path exists, if not whe should given an error
			if(!SpoonFile::exists(BACKEND_MODULES_PATH .'/'. $this->getModule() .'/js/'. $this->file))
			{
				// set correct headers
				SpoonHTTP::setHeadersByCode(404);

				// throw an exception, when debug is on we get a descent message
				throw new BackendException('File not present.');
			}
		}
	}


	/**
	 * Set language
	 *
	 * @return	void
	 * @param	string $value
	 */
	public function setLanguage($value)
	{
		// set property
		$this->language = (string) $value;

		// set the locale (we need this for the labels)
		BackendLanguage::setLocale(BackendAuthentication::getUser()->getSetting('backend_interface_language'));

		// set the working language
		BackendLanguage::setWorkingLanguage($this->language);
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

		// core is a module that contains general stuff, so it has to be allowed
		if($this->module !== 'core')
		{
			// is this module allowed?
			if(!BackendAuthentication::isAllowedModule($this->module))
			{
				// set correct headers
				SpoonHTTP::setHeadersByCode(403);

				// throw an exception, when debug is on we get a descent message
				throw new BackendException('Not allowed module.');
			}
		}

		// create url instance, the templatemodifiers need this object
		$url = new BackendURL();

		// set the module
		$url->setModule($this->module);
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