<?php

/**
 * BackendTemplate, this is our extended version of SpoonTemplate
 *
 * This class will handle a lot of stuff for you, for example:
 * 	- it will assign all labels
 *	- it will map some modifiers
 *  - it will assign a lot of constants
 * 	- ...
 *
 *
 * This source file is part of Fork CMS.
 *
 * @package		backend
 * @subpackage	template
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendTemplate extends SpoonTemplate
{
	/**
	 * Default constructor
	 * The constructor will store the instance in the reference, preset some settings and map the custom modifiers.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// store in reference so we can access it from everywhere
		Spoon::setObjectReference('template', $this);

		// set cache directory
		$this->setCacheDirectory(BACKEND_CACHE_PATH .'/cached_templates');

		// set compile directory
		$this->setCompileDirectory(BACKEND_CACHE_PATH .'/templates');

		// when debugging the template should be recompiled every time
		$this->setForceCompile(SPOON_DEBUG);

		// map custom modifiers
		$this->mapCustomModifiers();
	}


	/**
	 * Output the template into the browser
	 * Will also assign the interfacelabels and all user-defined constants.
	 *
	 * @return	void
	 * @param	string $template
	 */
	public function display($template)
	{
		// parse constants
		$this->parseConstants();

		// parse authenticated user
		$this->parseAuthenticatedUser();

		// parse the label
		$this->parseLabels();

		// call the parent
		parent::display($template);
	}


	/**
	 * Map the fork-specific modifiers
	 *
	 * @return	void
	 */
	private function mapCustomModifiers()
	{
		// convert vars into an url, syntax {$var|geturl:<pageId>}
		$this->mapModifier('geturl', array('BackendTemplateModifiers', 'getURL'));
		$this->mapModifier('getnavigation', array('BackendTemplateModifiers', 'getNavigation'));
	}


	/**
	 * Parse the settings for the authenticated user
	 *
	 * @return	void
	 */
	private function parseAuthenticatedUser()
	{
		if(BackendAuthentication::getUser()->isAuthenticated())
		{
			// show stuff that only should be visible if authenticated
			$this->assign('isAuthenticated', true);

			// get authenticated user-settings
			$aSettings = (array) BackendAuthentication::getUser()->getSettings();

			// loop settings
			foreach($aSettings as $key => $setting) $this->assign('authenticatedUser'. SpoonFilter::toCamelCase($key), $setting);

			// assign special vars
			$this->assign('authenticatedUserEditUrl', BackendModel::createURLForAction('edit', 'users') .'?id='. BackendAuthentication::getUser()->getUserId());
		}
	}


	/**
	 * Parse all user-defined constants
	 *
	 * @return	void
	 */
	private function parseConstants()
	{
		// constants that should be protected from usage in the template
		$aNotPublicConstants = array('DB_TYPE', 'DB_DATABASE', 'DB_HOSTNAME', 'DB_USERNAME', 'DB_PASSWORD');

		// get all defined constants
		$aConstants = get_defined_constants(true);

		// init var
		$aRealConstants = array();

		// remove protected constants aka constants that should not be used in the template
		foreach($aConstants['user'] as $key => $value)
		{
			if(!in_array($key, $aNotPublicConstants)) $aRealConstants[$key] = $value;
		}

		// we should only assign constants if there are constants to assign
		if(!empty($aRealConstants)) $this->assign($aRealConstants);

		// we use some abbrviations and common terms, these should also be assigned
		$this->assign('LANGUAGE', BackendLanguage::getWorkingLanguage());

		// get the url object, we need this for some template-constants
		$url = Spoon::getObjectReference('url');

		// assign the current module
		$this->assign('MODULE', $url->getModule());

		// assign the current action
		$this->assign('ACTION', $url->getAction());

		// is the user object filled?
		if(BackendAuthentication::getUser()->isAuthenticated())
		{
			// assign the authenticated users secret key
			$this->assign('SECRET_KEY', BackendAuthentication::getUser()->getSecretKey());
		}

		// @todo	settings
//		$this->assign('SITE_TITLE', BackendModel::getModuleSetting('core', 'site_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE));
	}


	/**
	 * Assign the labels
	 *
	 * @return	void
	 */
	private function parseLabels()
	{
		// get the url from the reference, we need to know which module is requested
		$url = Spoon::getObjectReference('url');

		// grab the current module
		$currentModule = $url->getModule();

		// init vars
		$aRealErrors = array();
		$aRealLabels = array();
		$aRealMessages = array();

		// get all errors
		$aErrors = BackendLanguage::getErrors();

		// get all labels
		$aLabels = BackendLanguage::getLabels();

		// get all messages
		$aMessages = (array) BackendLanguage::getMessages();

		// set the begin state
		$aRealErrors = $aErrors['core'];
		$aRealLabels = $aLabels['core'];
		$aRealMessages = $aMessages['core'];

		// loop all errors, label, messages and add them again, but prefixed with Core. So we can decide in the
		// template to use the core-value instead of the one set by the module
		foreach($aErrors['core'] as $key => $value) $aRealErrors['Core'. $key] = $value;
		foreach($aLabels['core'] as $key => $value) $aRealLabels['Core'. $key] = $value;
		foreach($aMessages['core'] as $key => $value) $aRealMessages['Core'. $key] = $value;

		// are there errors for the current module?
		if(isset($aErrors[$currentModule]))
		{
			// loop the module-specific errors and reset them in the array with values we will use
			foreach($aErrors[$currentModule] as $key => $value) $aRealErrors[$key] = $value;
		}

		// are there labels for the current module?
		if(isset($aLabels[$currentModule]))
		{
			// loop the module-specific labels and reset them in the array with values we will use
			foreach($aLabels[$currentModule] as $key => $value) $aRealLabels[$key] = $value;
		}

		// are there messages for the current module?
		if(isset($aMessages[$currentModule]))
		{
			// loop the module-specific errors and reset them in the array with values we will use
			foreach($aMessages[$currentModule] as $key => $value) $aRealMessages[$key] = $value;
		}

		// sort the arrays (just to make it look beautifull)
		ksort($aRealErrors);
		ksort($aRealLabels);
		ksort($aRealMessages);

		// assign errors
		$this->assignArray($aRealErrors, 'err');

		// assign labels
		$this->assignArray($aRealLabels, 'lbl');

		// assign messages
		$this->assignArray($aRealMessages, 'msg');
	}
}


/**
 * BackendTemplateModifiers, this is our class with custom modifiers.
 *
 * This source file is part of Fork CMS.
 *
 * @package		backend
 * @subpackage	template
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendTemplateModifiers
{
	/**
	 * Convert a var into a url
	 * 	syntax: {$var|geturl:<action>[:<module>]}
	 *
	 * @return	void
	 * @param	string[optional] $var
	 * @param	string $action
	 * @param	string[optional] $module
	 */
	public static function getURL($var = null, $action, $module = null)
	{
		// redefine
		$var = (string) $var;
		$action = (string) $action;
		$module = ($module !== null) ? (string) $module : $url->getModule();

		// get url
		$url = Spoon::getObjectReference('url');

		// build url and return it
		return '/'. NAMED_APPLICATION .'/'. BackendLanguage::getWorkingLanguage() .'/'. $module .'/'. $action;
	}


	/**
	 * Convert a var into navigation-html
	 *  syntax: {$var|getnavigation:startdepth[:maximumdepth]}
	 *
	 * @return	void
	 * @param	string[optional] $var
	 * @param	int $startDepth
	 * @param	int[optional] $maximumDepth
	 */
	public static function getNavigation($var = null, $startDepth, $maximumDepth = null)
	{
		// get navigation
		$navigation = Spoon::getObjectReference('navigation');

		// redefine
		$var = (string) $var;
		$startDepth = (int) $startDepth;
		$maximumDepth = ($maximumDepth !== null) ? (int) $maximumDepth : null;

		return $navigation->getNavigation($startDepth, $maximumDepth);
	}

}

?>