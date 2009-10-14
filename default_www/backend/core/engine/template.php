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
 * @subpackage	core
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

		// parse locale
		$this->parseLocale();

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
		$this->mapModifier('dump', array('BackendTemplateModifiers', 'dump'));
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
			$settings = (array) BackendAuthentication::getUser()->getSettings();

			// loop settings
			foreach($settings as $key => $setting) $this->assign('authenticatedUser'. SpoonFilter::toCamelCase($key), $setting);

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
		$notPublicConstants = array('DB_TYPE', 'DB_DATABASE', 'DB_HOSTNAME', 'DB_USERNAME', 'DB_PASSWORD');

		// get all defined constants
		$constants = get_defined_constants(true);

		// init var
		$realConstants = array();

		// remove protected constants aka constants that should not be used in the template
		foreach($constants['user'] as $key => $value)
		{
			if(!in_array($key, $notPublicConstants)) $realConstants[$key] = $value;
		}

		// we should only assign constants if there are constants to assign
		if(!empty($realConstants)) $this->assign($realConstants);

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

			// assign the authentiated users preferred interface language
			$this->assign('INTERFACE_LANGUAGE', (string) BackendAuthentication::getUser()->getSetting('backend_interface_language'));
		}

		// assign some variable constants (such as site-title)
		$this->assign('SITE_TITLE', BackendModel::getModuleSetting('core', 'site_title_'. BackendLanguage::getWorkingLanguage(), SITE_DEFAULT_TITLE));
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
		$realErrors = array();
		$realLabels = array();
		$realMessages = array();

		// get all errors
		$errors = BackendLanguage::getErrors();

		// get all labels
		$labels = BackendLanguage::getLabels();

		// get all messages
		$messages = (array) BackendLanguage::getMessages();

		// set the begin state
		$realErrors = $errors['core'];
		$realLabels = $labels['core'];
		$realMessages = $messages['core'];

		// loop all errors, label, messages and add them again, but prefixed with Core. So we can decide in the
		// template to use the core-value instead of the one set by the module
		foreach($errors['core'] as $key => $value) $realErrors['Core'. $key] = $value;
		foreach($labels['core'] as $key => $value) $realLabels['Core'. $key] = $value;
		foreach($messages['core'] as $key => $value) $realMessages['Core'. $key] = $value;

		// are there errors for the current module?
		if(isset($errors[$currentModule]))
		{
			// loop the module-specific errors and reset them in the array with values we will use
			foreach($errors[$currentModule] as $key => $value) $realErrors[$key] = $value;
		}

		// are there labels for the current module?
		if(isset($labels[$currentModule]))
		{
			// loop the module-specific labels and reset them in the array with values we will use
			foreach($labels[$currentModule] as $key => $value) $realLabels[$key] = $value;
		}

		// are there messages for the current module?
		if(isset($messages[$currentModule]))
		{
			// loop the module-specific errors and reset them in the array with values we will use
			foreach($messages[$currentModule] as $key => $value) $realMessages[$key] = $value;
		}

		// sort the arrays (just to make it look beautifull)
		ksort($realErrors);
		ksort($realLabels);
		ksort($realMessages);

		// assign errors
		$this->assignArray($realErrors, 'err');

		// assign labels
		$this->assignArray($realLabels, 'lbl');

		// assign messages
		$this->assignArray($realMessages, 'msg');
	}


	private function parseLocale()
	{
		// init vars
		$localeToAssign = array();

		// get months
		$monthsLong = SpoonLocale::getMonths(BackendLanguage::getInterfaceLanguage(), false);
		$monthsShort = SpoonLocale::getMonths(BackendLanguage::getInterfaceLanguage(), true);

		// get days
		$daysLong = SpoonLocale::getWeekDays(BackendLanguage::getInterfaceLanguage(), false, 'sunday');
		$daysShort = SpoonLocale::getWeekDays(BackendLanguage::getInterfaceLanguage(), true, 'sunday');

		// build labels
		foreach($monthsLong as $key => $value) $localeToAssign['locMonthLong'. $key] = $value;
		foreach($monthsShort as $key => $value) $localeToAssign['locMonthShort'. $key] = $value;
		foreach($daysLong as $key => $value) $localeToAssign['locDayLong'. $key] = $value;
		foreach($daysShort as $key => $value) $localeToAssign['locDayShort'. $key] = $value;

		// assign
		$this->assignArray($localeToAssign);
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
	 * Dumps the data
	 *
	 * @return	string
	 * @param	string $var
	 */
	public static function dump($var)
	{
		return Spoon::dump($var, false);
	}


	/**
	 * Convert a var into a url
	 * 	syntax: {$var|geturl:<action>[:<module>]}
	 *
	 * @return	void
	 * @param	string[optional] $var
	 * @param	string $action
	 * @param	string[optional] $module
	 */
	public static function getURL($var = null, $action = null, $module = null)
	{
		// get url
		$url = Spoon::getObjectReference('url');

		// redefine
		$var = (string) $var;
		$action = ($action !== null) ? (string) $action : $url->getAction();
		$module = ($module !== null) ? (string) $module : $url->getModule();

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