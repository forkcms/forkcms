<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

/**
 * This class will handle cronjob related stuff
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter.vandeneynde@wijs.be>
 */
class BackendCronjob extends BackendBaseObject implements ApplicationInterface
{
	/**
	 * @var BackendBaseCronjob
	 */
	private $cronjob;

	/**
	 * @var	int
	 */
	private $id;

	/**
	 * @var	string
	 */
	private $language;

	/**
	 * @return Symfony\Component\HttpFoundation\Response
	 */
	public function display()
	{
		$this->cronjob->execute();

		// a cronjob does not have output, so we return a empty string as response
		// this is not a correct solution, in time cronjobs should have there own frontcontroller.
		return new Response('');
	}

	/**
	 * Execute the action
	 * We will build the classname, require the class and call the execute method.
	 */
	protected function execute()
	{
		// build action-class-name
		$actionClassName = 'Backend' . SpoonFilter::toCamelCase($this->getModule() . '_cronjob_' . $this->getAction());

		if($this->getModule() == 'core')
		{
			// check if the file is present? If it isn't present there is a huge problem, so we will stop our code by throwing an error
			if(!SpoonFile::exists(BACKEND_CORE_PATH . '/cronjobs/' . $this->getAction() . '.php'))
			{
				// set correct headers
				SpoonHTTP::setHeadersByCode(500);

				// throw exception
				throw new BackendException('The cronjobfile for the module (' . $this->getAction() . '.php) can\'t be found.');
			}

			// require the config file, we know it is there because we validated it before (possible actions are defined by existence of the file).
			require_once BACKEND_CORE_PATH . '/cronjobs/' . $this->getAction() . '.php';
		}

		else
		{
			// check if the file is present? If it isn't present there is a huge problem, so we will stop our code by throwing an error
			if(!SpoonFile::exists(BACKEND_MODULES_PATH . '/' . $this->getModule() . '/cronjobs/' . $this->getAction() . '.php'))
			{
				// set correct headers
				SpoonHTTP::setHeadersByCode(500);

				// throw exception
				throw new BackendException('The cronjobfile for the module (' . $this->getAction() . '.php) can\'t be found.');
			}

			// require the config file, we know it is there because we validated it before (possible actions are defined by existence of the file).
			require_once BACKEND_MODULES_PATH . '/' . $this->getModule() . '/cronjobs/' . $this->getAction() . '.php';
		}

		// validate if class exists (aka has correct name)
		if(!class_exists($actionClassName))
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(500);

			// throw exception
			throw new BackendException('The cronjobfile is present, but the classname should be: ' . $actionClassName . '.');
		}

		// create action-object
		$this->cronjob = new $actionClassName($this->getKernel());
		$this->cronjob->setModule($this->getModule());
		$this->cronjob->setAction($this->getAction());
	}

	/**
	 * This method exists because the service container needs to be set before
	 * the page's functionality gets loaded.
	 */
	public function initialize()
	{
		// because some cronjobs will be run on the command line we should pass parameters
		if(isset($_SERVER['argv']))
		{
			// init var
			$first = true;

			// loop all passes arguments
			foreach($_SERVER['argv'] as $parameter)
			{
				// ignore first, because this is the scripts name.
				if($first)
				{
					// reset
					$first = false;

					// skip
					continue;
				}

				// split into chunks
				$chunks = explode('=', $parameter, 2);

				// valid parameters?
				if(count($chunks) == 2)
				{
					// build key and value
					$key = trim($chunks[0], '--');
					$value = $chunks[1];

					// set in GET
					if($key != '' && $value != '') $_GET[$key] = $value;
				}
			}
		}

		// define the Named Application
		if(!defined('NAMED_APPLICATION')) define('NAMED_APPLICATION', 'backend');

		// set the module
		$this->setModule(SpoonFilter::getGetValue('module', null, ''));

		// set the requested file
		$this->setAction(SpoonFilter::getGetValue('action', null, ''));

		// set the language
		$this->setLanguage(SpoonFilter::getGetValue('language', FrontendLanguage::getActiveLanguages(), SITE_DEFAULT_LANGUAGE));

		// mark cronjob as run
		$cronjobs = (array) BackendModel::getModuleSetting('core', 'cronjobs');
		$cronjobs[] = $this->getModule() . '.' . $this->getAction();
		BackendModel::setModuleSetting('core', 'cronjobs', array_unique($cronjobs));

		$this->execute();
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
	 * Set the action
	 *
	 * We can't rely on the parent setModule function, because a cronjob requires no login
	 *
	 * @param string $action The action to load.
	 * @param string[optional] $module The module to load.
	 */
	public function setAction($action, $module = null)
	{
		// set module
		if($module !== null) $this->setModule($module);

		// check if module is set
		if($this->getModule() === null) throw new BackendException('Module has not yet been set.');

		// path to look for actions based on the module
		if($this->getModule() == 'core') $path = BACKEND_CORE_PATH . '/cronjobs';
		else $path = BACKEND_MODULES_PATH . '/' . $this->getModule() . '/cronjobs';

		// does this module exist?
		$actions = SpoonFile::getList($path);
		if(!in_array($action . '.php', $actions))
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(403);

			// throw exception
			throw new BackendException('Action not allowed.');
		}

		// set property
		$this->action = (string) $action;
	}

	/**
	 * Set language
	 *
	 * @param string $value The language to load.
	 */
	public function setLanguage($value)
	{
		// get the possible languages
		$possibleLanguages = BackendLanguage::getWorkingLanguages();

		// validate
		if(!in_array($value, array_keys($possibleLanguages))) throw new BackendException('Invalid language.');

		// set property
		$this->language = $value;

		// set the locale (we need this for the labels)
		BackendLanguage::setLocale($this->language);

		// set working language
		BackendLanguage::setWorkingLanguage($this->language);
	}

	/**
	 * Set the module
	 *
	 * We can't rely on the parent setModule function, because a cronjob requires no login
	 *
	 * @param string $module The module to load.
	 */
	public function setModule($module)
	{
		// does this module exist?
		$modules = SpoonDirectory::getList(BACKEND_MODULES_PATH);
		$modules[] = 'core';
		if(!in_array($module, $modules))
		{
			// set correct headers
			SpoonHTTP::setHeadersByCode(403);

			// throw exception
			throw new BackendException('Module not allowed.');
		}

		// set property
		$this->module = $module;
	}
}
