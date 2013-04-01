<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This class is the real code, it creates an action, loads the configfile, ...
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Dave Lens <dave.lens@wijs.be>
 */
class BackendAction extends BackendBaseObject
{
	/**
	 * The config file
	 *
	 * @var	BackendBaseConfig
	 */
	private $config;

	/**
	 * BackendTemplate
	 *
	 * @var	BackendTemplate
	 */
	public $tpl;

	/**
	 * You have to specify the action and module so we know what to do with this instance
	 */
	public function __construct()
	{
		// grab stuff from the reference and store them in this object (for later/easy use)
		$this->tpl = Spoon::get('template');
	}

	/**
	 * Execute the action
	 * We will build the classname, require the class and call the execute method.
	 */
	public function execute()
	{
		$this->loadConfig();

		// is the requested action possible? If not we throw an exception. We don't redirect because that could trigger a redirect loop
		if(!in_array($this->getAction(), $this->config->getPossibleActions()))
		{
			throw new BackendException('This is an invalid action (' . $this->getAction() . ').');
		}

		// build action-class-name
		$actionClassName = SpoonFilter::toCamelCase('backend_' . $this->getModule() . '_' . $this->getAction());

		// require the config file, we know it is there because we validated it before (possible actions are defined by existence off the file).
		require_once BACKEND_MODULE_PATH . '/actions/' . $this->getAction() . '.php';

		// validate if class exists (aka has correct name)
		if(!class_exists($actionClassName)) throw new BackendException('The actionfile is present, but the classname should be: ' . $actionClassName . '.');

		// get working languages
		$languages = BackendLanguage::getWorkingLanguages();
		$workingLanguages = array();

		// loop languages and build an array that we can assign
		foreach($languages as $abbreviation => $label) $workingLanguages[] = array('abbr' => $abbreviation, 'label' => $label, 'selected' => ($abbreviation == BackendLanguage::getWorkingLanguage()));

		// assign the languages
		$this->tpl->assign('workingLanguages', $workingLanguages);

		// create action-object
		$object = new $actionClassName();
		$object->setKernel($this->getKernel());
		$object->execute();
		return $object->getContent();
	}

	/**
	 * Load the config file for the requested module.
	 * In the config file we have to find disabled actions, the constructor will read the folder and set possible actions
	 * Other configurations will be stored in it also.
	 */
	public function loadConfig()
	{
		// check if module path is not yet defined
		if(!defined('BACKEND_MODULE_PATH'))
		{
			// build path for core
			if($this->getModule() == 'core') define('BACKEND_MODULE_PATH', BACKEND_PATH . '/' . $this->getModule());

			// build path to the module and define it. This is a constant because we can use this in templates.
			else define('BACKEND_MODULE_PATH', BACKEND_MODULES_PATH . '/' . $this->getModule());
		}

		// check if the config is present? If it isn't present there is a huge problem, so we will stop our code by throwing an error
		if(!SpoonFile::exists(BACKEND_MODULE_PATH . '/config.php')) throw new BackendException('The configfile for the module (' . $this->getModule() . ') can\'t be found.');

		// build config-object-name
		$configClassName = 'Backend' . SpoonFilter::toCamelCase($this->getModule() . '_config');

		// require the config file, we validated before for existence.
		require_once BACKEND_MODULE_PATH . '/config.php';

		// validate if class exists (aka has correct name)
		if(!class_exists($configClassName)) throw new BackendException('The config file is present, but the classname should be: ' . $configClassName . '.');

		// create config-object, the constructor will do some magic
		$this->config = new $configClassName($this->getModule());

		// set action
		$action = ($this->config->getDefaultAction() !== null) ? $this->config->getDefaultAction() : 'index';

		// require the model if it exists
		if(SpoonFile::exists(BACKEND_MODULES_PATH . '/' . $this->config->getModule() . '/engine/model.php'))
		{
			require_once BACKEND_MODULES_PATH . '/' . $this->config->getModule() . '/engine/model.php';
		}
	}
}
