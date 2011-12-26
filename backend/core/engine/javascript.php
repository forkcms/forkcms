<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class will handle files JS-files that have to be parsed by PHP
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@netlash.com>
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

	public function __construct()
	{
		// define the Named Application
		if(!defined('NAMED_APPLICATION')) define('NAMED_APPLICATION', 'backend');

		// set the module
		$this->setModule(SpoonFilter::getGetValue('module', null, ''));

		// set the requested file
		$this->setFile(SpoonFilter::getGetValue('file', null, ''));

		// set the language
		$this->setLanguage(SpoonFilter::getGetValue('language', array_keys(BackendLanguage::getWorkingLanguages()), SITE_DEFAULT_LANGUAGE));

		// build the path
		if($this->module == 'core') $path = BACKEND_CORE_PATH . '/js/' . $this->getFile();
		else $path = BACKEND_MODULES_PATH . '/' . $this->getModule() . '/js/' . $this->getFile();

		// set correct headers
		SpoonHTTP::setHeaders('content-type: application/javascript');

		// create a new template instance (this will handle all stuff for us)
		$tpl = new BackendTemplate();

		// enable addslashes on each locale
		$tpl->setAddSlashes(true);

		// display
		$tpl->display($path, true);
	}

	/**
	 * Get file
	 *
	 * @return string
	 */
	public function getFile()
	{
		return $this->filename;
	}

	/**
	 * Get language
	 *
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
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
	 * Set file
	 *
	 * @param string $value The file to load.
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
			$path = realpath(BACKEND_CORE_PATH . '/js/' . $this->filename);

			// validate if path is allowed
			if(substr($path, 0, strlen(realpath(BACKEND_CORE_PATH . '/js/'))) != realpath(BACKEND_CORE_PATH . '/js/')) $valid = false;
		}

		// not core
		else
		{
			// build path
			$path = realpath(BACKEND_MODULES_PATH . '/' . $this->getModule() . '/js/' . $this->filename);

			// validate if path is allowed
			if(substr($path, 0, strlen(realpath(BACKEND_MODULES_PATH . '/' . $this->getModule() . '/js/'))) != realpath(BACKEND_MODULES_PATH . '/' . $this->getModule() . '/js/')) $valid = false;
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
	 * @param string $value The language to load.
	 */
	private function setLanguage($value)
	{
		// set property
		$this->language = (string) $value;

		// is this a authenticated user?
		if(BackendAuthentication::isLoggedIn())
		{
			$language = BackendAuthentication::getUser()->getSetting('interface_language');
		}

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
	 * @param string $value The module to use.
	 */
	private function setModule($value)
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
