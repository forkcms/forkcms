<?php

/**
 * This class will handle files JS-files that have to be parsed by PHP
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class BackendJavascript
{
	/**
	 * The actual filename
	 *
	 * @var	string
	 */
	private $filename;


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
		// define the Named Application
		if(!defined('NAMED_APPLICATION')) define('NAMED_APPLICATION', 'backend');

		// set the module
		$this->setModule(SpoonFilter::getGetValue('module', null, ''));

		// set the requested file
		$this->setFile(SpoonFilter::getGetValue('file', null, ''));

		// set the language
		$this->setLanguage(SpoonFilter::getGetValue('language', BackendLanguage::getActiveLanguages(), SITE_DEFAULT_LANGUAGE));

		// create a new template instance (this will handle all stuff for us)
		$tpl = new BackendTemplate();

		// set correct headers
		SpoonHTTP::setHeaders('content-type: application/javascript');

		// output the template
		if($this->module == 'core') $tpl->display(BACKEND_CORE_PATH . '/js/' . $this->getFile(), true);
		else $tpl->display(BACKEND_MODULES_PATH . '/' . $this->getModule() . '/js/' . $this->getFile(), true);
	}


	/**
	 * Get file
	 *
	 * @return	string
	 */
	public function getFile()
	{
		return $this->filename;
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
	 * @param	string $value		The file to load.
	 */
	public function setFile($value)
	{
		// set property
		$this->filename = (string) $value;

		// core is a special module
		if($this->module == 'core')
		{
			// check if the path exists, if not whe should given an error
			if(!SpoonFile::exists(BACKEND_CORE_PATH . '/js/' . $this->filename))
			{
				// set correct headers
				SpoonHTTP::setHeadersByCode(404);

				// when debug is on throw an exception
				if(SPOON_DEBUG) throw new BackendException('File not present.');

				// when debug is of show a descent message
				else exit(SPOON_DEBUG_MESSAGE);
			}
		}

		// not core
		else
		{
			// check if the path exists, if not whe should given an error
			if(!SpoonFile::exists(BACKEND_MODULES_PATH . '/' . $this->getModule() . '/js/' . $this->filename))
			{
				// set correct headers
				SpoonHTTP::setHeadersByCode(404);

				// when debug is on throw an exception
				if(SPOON_DEBUG) throw new BackendException('File not present.');

				// when debug is of show a descent message
				else exit(SPOON_DEBUG_MESSAGE);
			}
		}
	}


	/**
	 * Set language
	 *
	 * @return	void
	 * @param	string $value	The language to load.
	 */
	public function setLanguage($value)
	{
		// set property
		$this->language = (string) $value;

		// is this a authenticated user?
		if(BackendAuthentication::isLoggedIn()) $language = BackendAuthentication::getUser()->getSetting('interface_language');

		// unknown user (fallback to default language)
		else $language = BackendModel::getModuleSetting('core', 'default_interface_language');

		// set the locale (we need this for the labels)
		BackendLanguage::setLocale($language);

		// set the working language
		BackendLanguage::setWorkingLanguage($this->language);
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

		// core is a module that contains general stuff, so it has to be allowed
		if($this->module !== 'core')
		{
			// is this module allowed?
			if(!BackendAuthentication::isAllowedModule($this->module))
			{
				// set correct headers
				SpoonHTTP::setHeadersByCode(403);

				// stop script execution
				exit;
			}
		}

		// create URL instance, the templatemodifiers need this object
		$URL = new BackendURL();

		// set the module
		$URL->setModule($this->module);
	}
}

?>