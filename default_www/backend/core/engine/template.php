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
 * @author		Davy Hellemans <davy@netlash.com>
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
	 * @param	bool[optional] $customHeaders
	 */
	public function display($template, $customHeaders = false)
	{
		// parse constants
		$this->parseConstants();

		// parse authenticated user
		$this->parseAuthenticatedUser();

		// check debug
		$this->parseDebug();

		// parse the label
		$this->parseLabels();

		// parse locale
		$this->parseLocale();

		// asign a placeholder var
		$this->assign('var', '');

		// parse headers
		if(!$customHeaders) SpoonHTTP::setHeaders('content-type: text/html;charset=utf-8');

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
		$this->mapModifier('getURL', array('BackendTemplateModifiers', 'getURL'));

		// convert var into navigation, syntax {$var|getnavigation:<startdepth>:<enddepth>}
		$this->mapModifier('getnavigation', array('BackendTemplateModifiers', 'getNavigation'));
		$this->mapModifier('getNavigation', array('BackendTemplateModifiers', 'getNavigation'));

		// convert var into navigation, syntax {$var|getmainnavigation}
		$this->mapModifier('getmainnavigation', array('BackendTemplateModifiers', 'getMainNavigation'));
		$this->mapModifier('getMainNavigation', array('BackendTemplateModifiers', 'getMainNavigation'));

		// string
		$this->mapModifier('truncate', array('BackendTemplateModifiers', 'truncate'));

		// debug stuff
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
			foreach($settings as $key => $setting)
			{
				// redefine setting
				$setting = ($setting === null) ? '' : $setting;

				// assign setting
				$this->assign('authenticatedUser'. SpoonFilter::toCamelCase($key), $setting);
			}

			// assign special vars
			$this->assign('authenticatedUserEditUrl', BackendModel::createURLForAction('edit', 'users') .'&id='. BackendAuthentication::getUser()->getUserId());
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
		$this->assign('SITE_TITLE', BackendModel::getSetting('core', 'site_title_'. BackendLanguage::getWorkingLanguage(), SITE_DEFAULT_TITLE));
	}


	/**
	 * Assigns an option if we are in debug-mode
	 *
	 * @return void
	 */
	private function parseDebug()
	{
		// @todo for now we only check if SPOON_DEBUG is true
		if(SPOON_DEBUG) $this->assign('debug', true);
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
		$messages = BackendLanguage::getMessages();

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


	/**
	 * Parse the locale (things like months, days, ...)
	 *
	 * @return	void
	 */
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
		foreach($monthsLong as $key => $value) $localeToAssign['locMonthLong'. ucfirst($key)] = $value;
		foreach($monthsShort as $key => $value) $localeToAssign['locMonthShort'. ucfirst($key)] = $value;
		foreach($daysLong as $key => $value) $localeToAssign['locDayLong'. ucfirst($key)] = $value;
		foreach($daysShort as $key => $value) $localeToAssign['locDayShort'. ucfirst($key)] = $value;

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
// @todo tijs - moeten we niemer alfabetisch werken?
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
		Spoon::dump($var, false);
	}


	/**
	 * Convert a var into a url
	 * 	syntax: {$var|geturl:<action>[:<module>]}
	 *
	 * @return	void
	 * @param	string[optional] $var
	 * @param	string[optional] $action
	 * @param	string[optional] $module
	 */
	public static function getURL($var = null, $action = null, $module = null)
	{
		return BackendModel::createURLForAction($action, $module, BackendLanguage::getWorkingLanguage());
	}


	/**
	 * Convert a var into navigation-html
	 *  syntax: {$var|getnavigation:startdepth[:maximumdepth]}
	 *
	 * @return	string
	 * @param	string[optional] $var
	 */
	public static function getNavigation($var = null)
	{
		return  Spoon::getObjectReference('navigation')->getNavigation();
	}


	/**
	 * Convert a var into main-navigation-html
	 *  syntax: {$var|getmainnavigation}
	 *
	 * @return	string
	 * @param	string[optional] $var
	 */
	public static function getMainNavigation($var = null)
	{
		// get navigation
		$navigation = Spoon::getObjectReference('navigation');

		// redefine
		$var = (string) $var;

		// return
		return $navigation->getMainNavigation();
	}


	/**
	 * Truncate a string
	 *
	 * @return	string
	 * @param	string $var
	 * @param	int $length
	 * @param	bool[optional] $useHellip
	 */
	public static function truncate($var = null, $length, $useHellip = true)
	{
		// remove special chars
		$var = htmlspecialchars_decode($var);

		// less characters
		if(mb_strlen($var) <= $length) return SpoonFilter::htmlspecialchars($var);

		// more characters
		else
		{
			// hellip is seen as 1 char, so remove it from length
			if($useHellip) $length = $length - 1;

			// get the amount of requested characters
			$var = mb_substr($var, 0, $length);

			// add hellip
			if($useHellip) $var .= '…';

			// return
			return SpoonFilter::htmlspecialchars($var);
		}
	}

}

?>