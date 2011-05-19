<?php

/**
 * This is our extended version of SpoonTemplate
 * This class will handle a lot of stuff for you, for example:
 * 	- it will assign all labels
 * 	- it will map some modifiers
 * 	- it will assign a lot of constants
 * 	- ...
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendTemplate extends SpoonTemplate
{
	/**
	 * URL instance
	 *
	 * @var	BackendURL
	 */
	private $URL;


	/**
	 * Default constructor
	 * The constructor will store the instance in the reference, preset some settings and map the custom modifiers.
	 *
	 * @return	void
	 * @param	bool[optional] $addToReference		Should the instance be added into the reference.
	 */
	public function __construct($addToReference = true)
	{
		// parent constructor
		parent::__construct();

		// get URL instance
		if(Spoon::exists('url')) $this->URL = Spoon::get('url');

		// store in reference so we can access it from everywhere
		if($addToReference) Spoon::set('template', $this);

		// set cache directory
		$this->setCacheDirectory(BACKEND_CACHE_PATH . '/cached_templates');

		// set compile directory
		$this->setCompileDirectory(BACKEND_CACHE_PATH . '/compiled_templates');

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
	 * @param	string $template				The path for the template.
	 * @param	bool[optional] $customHeaders	Are there custom headers set?
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

		// parse some vars
		$this->parseVars();

		// parse headers
		if(!$customHeaders) SpoonHTTP::setHeaders('Content-type: text/html;charset=utf-8');

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
		// convert var into an URL, syntax {$var|geturl:<pageId>}
		$this->mapModifier('geturl', array('BackendTemplateModifiers', 'getURL'));

		// convert var into navigation, syntax {$var|getnavigation:<startdepth>:<enddepth>}
		$this->mapModifier('getnavigation', array('BackendTemplateModifiers', 'getNavigation'));

		// convert var into navigation, syntax {$var|getmainnavigation}
		$this->mapModifier('getmainnavigation', array('BackendTemplateModifiers', 'getMainNavigation'));

		// rand
		$this->mapModifier('rand', array('BackendTemplateModifiers', 'random'));

		// string
		$this->mapModifier('formatfloat', array('BackendTemplateModifiers', 'formatFloat'));
		$this->mapModifier('truncate', array('BackendTemplateModifiers', 'truncate'));

		// debug stuff
		$this->mapModifier('dump', array('BackendTemplateModifiers', 'dump'));

		// dates
		$this->mapModifier('formatdate', array('BackendTemplateModifiers', 'formatDate'));
		$this->mapModifier('formattime', array('BackendTemplateModifiers', 'formatTime'));
		$this->mapModifier('formatdatetime', array('BackendTemplateModifiers', 'formatDateTime'));

		// numbers
		$this->mapModifier('formatnumber', array('BackendTemplateModifiers', 'formatNumber'));

		// label (locale)
		$this->mapModifier('tolabel', array('BackendTemplateModifiers', 'toLabel'));
	}


	/**
	 * Parse the settings for the authenticated user
	 *
	 * @return	void
	 */
	private function parseAuthenticatedUser()
	{
		// check if the current user is authenticated
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
				$this->assign('authenticatedUser' . SpoonFilter::toCamelCase($key), $setting);
			}

			// assign special vars
			$this->assign('authenticatedUserEditUrl', BackendModel::createURLForAction('edit', 'users', null, array('id' => BackendAuthentication::getUser()->getUserId())));
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
		$notPublicConstants = array('DB_TYPE', 'DB_DATABASE', 'DB_HOSTNAME', 'DB_PORT', 'DB_USERNAME', 'DB_PASSWORD');

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

		if($this->URL instanceof BackendURL)
		{
			// assign the current module
			$this->assign('MODULE', $this->URL->getModule());

			// assign the current action
			$this->assign('ACTION', $this->URL->getAction());
		}

		// is the user object filled?
		if(BackendAuthentication::getUser()->isAuthenticated())
		{
			// assign the authenticated users secret key
			$this->assign('SECRET_KEY', BackendAuthentication::getUser()->getSecretKey());

			// assign the authentiated users preferred interface language
			$this->assign('INTERFACE_LANGUAGE', (string) BackendAuthentication::getUser()->getSetting('interface_language'));
		}

		// assign some variable constants (such as site-title)
		$this->assign('SITE_TITLE', BackendModel::getModuleSetting('core', 'site_title_' . BackendLanguage::getWorkingLanguage(), SITE_DEFAULT_TITLE));

		// theme
		if(BackendModel::getModuleSetting('core', 'theme') !== null)
		{
			$this->assign('THEME', BackendModel::getModuleSetting('core', 'theme'));
			$this->assign('THEME_PATH', FRONTEND_PATH . '/themes/' . BackendModel::getModuleSetting('core', 'theme'));
			$this->assign('THEME_HAS_CSS', (SpoonFile::exists(FRONTEND_PATH . '/themes/' . BackendModel::getModuleSetting('core', 'theme') . '/core/layout/css/screen.css')));
			$this->assign('THEME_HAS_EDITOR_CSS', (SpoonFile::exists(FRONTEND_PATH . '/themes/' . BackendModel::getModuleSetting('core', 'theme') . '/core/layout/css/editor_content.css')));
		}
	}


	/**
	 * Assigns an option if we are in debug-mode
	 *
	 * @return	void
	 */
	private function parseDebug()
	{
		$this->assign('debug', SPOON_DEBUG);
	}


	/**
	 * Assign the labels
	 *
	 * @return	void
	 */
	private function parseLabels()
	{
		// grab the current module
		if($this->URL instanceof BackendURL) $currentModule = $this->URL->getModule();
		else $currentModule = 'core';

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
		foreach($errors['core'] as $key => $value) $realErrors['Core' . $key] = $value;
		foreach($labels['core'] as $key => $value) $realLabels['Core' . $key] = $value;
		foreach($messages['core'] as $key => $value) $realMessages['Core' . $key] = $value;

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
		foreach($monthsLong as $key => $value) $localeToAssign['locMonthLong' . ucfirst($key)] = $value;
		foreach($monthsShort as $key => $value) $localeToAssign['locMonthShort' . ucfirst($key)] = $value;
		foreach($daysLong as $key => $value) $localeToAssign['locDayLong' . ucfirst($key)] = $value;
		foreach($daysShort as $key => $value) $localeToAssign['locDayShort' . ucfirst($key)] = $value;

		// assign
		$this->assignArray($localeToAssign);
	}


	/**
	 * Parse some vars
	 *
	 * @return	void
	 */
	private function parseVars()
	{
		// assign a placeholder var
		$this->assign('var', '');

		// assign current timestamp
		$this->assign('timestamp', time());

		// assign body ID
		if($this->URL instanceof BackendURL)
		{
			$this->assign('bodyID', SpoonFilter::toCamelCase($this->URL->getModule(), '_', true));

			// build classes
			$bodyClass = SpoonFilter::toCamelCase($this->URL->getModule() . '_' . $this->URL->getAction(), '_', true);

			// special occasions
			if($this->URL->getAction() == 'add' || $this->URL->getAction() == 'edit') $bodyClass = $this->URL->getModule() . 'AddEdit';

			// assign
			$this->assign('bodyClass', $bodyClass);
		}
	}
}


/**
 * This is our class with custom modifiers.
 *
 * @package		backend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendTemplateModifiers
{
	/**
	 * Dumps the data
	 * 	syntax: {$var|dump}
	 *
	 * @return	string
	 * @param	string $var		The variable to dump.
	 */
	public static function dump($var)
	{
		Spoon::dump($var, false);
	}


	/**
	 * Format a UNIX-timestamp as a date
	 * 	syntax: {$var|formatdate}
	 *
	 * @return	string
	 * @param	int $var	The UNIX-timestamp to format.
	 */
	public static function formatDate($var)
	{
		// get setting
		$format = BackendAuthentication::getUser()->getSetting('date_format');

		// format the date
		return SpoonDate::getDate($format, (int) $var, BackendLanguage::getInterfaceLanguage());
	}


	/**
	 * Format a UNIX-timestamp as a datetime
	 * 	syntax: {$var|formatdatetime}
	 *
	 * @return	string
	 * @param	int $var	The UNIX-timestamp to format.
	 */
	public static function formatDateTime($var)
	{
		// get setting
		$format = BackendAuthentication::getUser()->getSetting('datetime_format');

		// format the date
		return SpoonDate::getDate($format, (int) $var, BackendLanguage::getInterfaceLanguage());
	}


	/**
	 * Format a number as a float
	 *
	 * @return	string
	 * @param	float $number				The number to format.
	 * @param	int[optional] $decimals		The number of decimals.
	 */
	public static function formatFloat($number, $decimals = 2)
	{
		// redefine
		$number = (float) $number;
		$decimals = (int) $decimals;

		// get setting
		$format = BackendAuthentication::getUser()->getSetting('number_format', 'dot_nothing');

		// get separators
		$separators = explode('_', $format);
		$separatorSymbols = array('comma' => ',', 'dot' => '.', 'space' => ' ', 'nothing' => '');
		$decimalSeparator = (isset($separators[0], $separatorSymbols[$separators[0]]) ? $separatorSymbols[$separators[0]] : null);
		$thousandsSeparator = (isset($separators[1], $separatorSymbols[$separators[1]]) ? $separatorSymbols[$separators[1]] : null);

		// format the number
		return number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
	}


	/**
	 * Format a number
	 * 	syntax: {$var|formatnumber}
	 *
	 * @return	string
	 * @param	float $var		The number to format.
	 */
	public static function formatNumber($var)
	{
		// redefine
		$var = (float) $var;

		// get setting
		$format = BackendAuthentication::getUser()->getSetting('number_format', 'dot_nothing');

		// get amount of decimals
		$decimals = (strpos($var, '.') ? strlen(substr($var, strpos($var, '.') + 1)) : 0);

		// get separators
		$separators = explode('_', $format);
		$separatorSymbols = array('comma' => ',', 'dot' => '.', 'space' => ' ', 'nothing' => '');
		$decimalSeparator = (isset($separators[0], $separatorSymbols[$separators[0]]) ? $separatorSymbols[$separators[0]] : null);
		$thousandsSeparator = (isset($separators[1], $separatorSymbols[$separators[1]]) ? $separatorSymbols[$separators[1]] : null);

		// format the number
		return number_format($var, $decimals, $decimalSeparator, $thousandsSeparator);
	}


	/**
	 * Format a UNIX-timestamp as a date
	 * 	syntac: {$var|formatdate}
	 *
	 * @return	string
	 * @param	int $var	The UNIX-timestamp to format.
	 */
	public static function formatTime($var)
	{
		// get setting
		$format = BackendAuthentication::getUser()->getSetting('time_format');

		// format the date
		return SpoonDate::getDate($format, (int) $var, BackendLanguage::getInterfaceLanguage());
	}


	/**
	 * Convert a var into main-navigation-html
	 * 	syntax: {$var|getmainnavigation}
	 *
	 * @return	string
	 * @param	string[optional] $var	A placeholder var, will be replaced with the generated HTML.
	 */
	public static function getMainNavigation($var = null)
	{
		// redefine
		$var = (string) $var;

		return Spoon::get('navigation')->getNavigation(1, 1);
	}


	/**
	 * Convert a var into navigation-html
	 * 	syntax: {$var|getnavigation:startdepth[:maximumdepth]}
	 *
	 * @return	string
	 * @param	string[optional] $var		A placeholder var, will be replaced with the generated HTML.
	 * @param	int[optional] $startDepth	The start depth of the navigation to get.
	 * @param	int[optional] $endDepth		The ending depth of the navigation to get.
	 */
	public static function getNavigation($var = null, $startDepth = null, $endDepth = null)
	{
		// redefine
		$var = (string) $var;
		$startDepth = ($startDepth !== null) ? (int) $startDepth : 2;
		$endDepth = ($endDepth !== null) ? (int) $endDepth : null;

		// return navigation
		return Spoon::get('navigation')->getNavigation($startDepth, $endDepth);
	}


	/**
	 * Convert a var into a URL
	 * 	syntax: {$var|geturl:<action>[:<module>]}
	 *
	 * @return	void
	 * @param	string[optional] $var		A placeholder variable, it will be replaced with the URL.
	 * @param	string[optional] $action	The action to build the URL for.
	 * @param	string[optional] $module	The module to build the URL for.
	 * @param	string[optional] $suffix	A string to append.
	 */
	public static function getURL($var = null, $action = null, $module = null, $suffix = null)
	{
		// redefine
		$var = (string) $var;
		$action = ($action !== null) ? (string) $action : null;
		$module = ($module !== null) ? (string) $module : null;

		// build the url
		return BackendModel::createURLForAction($action, $module, BackendLanguage::getWorkingLanguage()) . $suffix;
	}


	/**
	 * Get a random var between a min and max
	 *
	 * @return	int
	 * @param	string[optional] $var	The string passed from the template.
	 * @param	int $min				The minimum number.
	 * @param	int $max				The maximim number.
	 */
	public static function random($var = null, $min, $max)
	{
		// redefine
		$var = (string) $var;
		$min = (int) $min;
		$max = (int) $max;

		// return
		return rand($min, $max);
	}


	/**
	 * Convert this string into a well formed label.
	 *
	 * @return	string
	 * @param	string $value	The value to convert to a label.
	 */
	public static function toLabel($value)
	{
		return ucfirst(BL::lbl(SpoonFilter::toCamelCase($value, '_', false)));
	}


	/**
	 * Truncate a string
	 * 	syntax: {$var|truncate:<max-length>[:<append-hellip>]}
	 *
	 * @return	string
	 * @param	string[optional] $var					A placeholder var, will be replaced with the generated HTML.
	 * @param	int $length					The maximum length of the truncated string.
	 * @param	bool[optional] $useHellip	Should a hellip be appended if the length exceeds the requested length?
	 */
	public static function truncate($var = null, $length, $useHellip = true)
	{
		// remove special chars
		$var = htmlspecialchars_decode($var, ENT_QUOTES);

		// remove HTML
		$var = strip_tags($var);

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
			return SpoonFilter::htmlspecialchars($var, ENT_QUOTES);
		}
	}
}

?>