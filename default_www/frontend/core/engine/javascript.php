<?php

/**
 * This class will handle files JS-files that have to be parsed by PHP
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@netlash.com>
 * @since		2.0
 */
class FrontendJavascript
{
	/**
	 * The actual filename
	 *
	 * @var	string
	 */
	private $filename;


	/**
	 * The language
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
		// if the application wasn't defined before we will define it
		if(!defined('NAMED_APPLICATION')) define('NAMED_APPLICATION', 'frontend');

		// set the module
		$this->setModule(SpoonFilter::getGetValue('module', null, ''));

		// set the requested file
		$this->setFile(SpoonFilter::getGetValue('file', null, ''));

		// set the language
		$this->setLanguage(SpoonFilter::getGetValue('language', FrontendLanguage::getActiveLanguages(), SITE_DEFAULT_LANGUAGE));

		// build the path
		if($this->module == 'core') $path = FRONTEND_CORE_PATH . '/js/' . $this->getFile();
		else $path = FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/js/' . $this->getFile();

		// set correct headers
		SpoonHTTP::setHeaders('content-type: application/javascript');

		// create a new template instance (this will handle all stuff for us)
		$tpl = new FrontendTemplate();

		// display
		$tpl->display(realpath($path), true);
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
	 * @param	string $value	The file to load.
	 */
	private function setFile($value)
	{
		// set property
		$this->filename = (string) $value;

		// validate
		if(substr_count($this->filename, '../') > 0)
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(400);

			// when debug is on throw an exception
			if(SPOON_DEBUG) throw new FrontendException('Invalid file.');

			// when debug is of show a descent message
			else exit(SPOON_DEBUG_MESSAGE);
		}

		// init var
		$valid = true;

		// core is a special module
		if($this->module == 'core')
		{
			// build path
			$path = realpath(FRONTEND_CORE_PATH . '/js/' . $this->filename);

			// validate if path is allowed
			if(substr($path, 0, strlen(FRONTEND_CORE_PATH . '/js/')) != FRONTEND_CORE_PATH . '/js/') $valid = false;
		}

		// not core
		else
		{
			// build path
			$path = realpath(FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/js/' . $this->filename);

			// validate if path is allowed
			if(substr($path, 0, strlen(FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/js/')) != FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/js/') $valid = false;
		}

		// invalid file?
		if(!$valid)
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(400);

			// when debug is on throw an exception
			if(SPOON_DEBUG) throw new FrontendException('Invalid file.');

			// when debug is of show a descent message
			else exit(SPOON_DEBUG_MESSAGE);
		}


		// check if the path exists, if not whe should given an error
		if(!SpoonFile::exists($path))
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(404);

			// when debug is on throw an exception
			if(SPOON_DEBUG) throw new FrontendException('File not present.');

			// when debug is of show a descent message
			else exit(SPOON_DEBUG_MESSAGE);
		}
	}


	/**
	 * Set language
	 *
	 * @return	void
	 * @param	string $value	The language.
	 */
	private function setLanguage($value)
	{
		// set property
		$this->language = (string) $value;

		// define constant
		define('FRONTEND_LANGUAGE', $this->language);

		// set the locale (we need this for the labels)
		FrontendLanguage::setLocale($this->language);
	}


	/**
	 * Set module
	 *
	 * @return	void
	 * @param	string $value	The module.
	 */
	private function setModule($value)
	{
		$this->module = (string) $value;
	}
}

?>