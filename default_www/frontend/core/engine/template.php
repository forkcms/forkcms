<?php

/**
 * FrontendTemplate, this is our extended version of SpoonTemplate
 * This class will handle a lot of stuff for you, for example:
 * 	- it will assign all labels
 *	- it will map some modifiers
 *  - it will assign a lot of constants
 * 	- ...
 *
 * @package		frontend
 * @subpackage	template
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendTemplate extends SpoonTemplate
{
	/**
	 * Default constructor
	 * The constructor will store the instance in the reference, preset some settings and map the custom modifiers.
	 *
	 * @return	void
	 * @param	bool[optional]	$dontAdd	Should the instance be added into the reference.
	 */
	public function __construct($dontAdd = false)
	{
		// store in reference so we can access it from everywhere
		if(!$dontAdd) Spoon::setObjectReference('template', $this);

		// set cache directory
		$this->setCacheDirectory(FRONTEND_CACHE_PATH .'/cached_templates');

		// set compile directory
		$this->setCompileDirectory(FRONTEND_CACHE_PATH .'/templates');

		// when debugging the template should be recompiled every time
		$this->setForceCompile(SPOON_DEBUG);

		// map custom modifiers
		$this->mapCustomModifiers();
	}


	/**
	 * Output the template into the browser
	 * Will also assign the labels and all user-defined constants.
	 * If you want custom-headers, you should set them yourself, otherwise the content-type and charset will be set
	 *
	 * @return	void
	 * @param	string $template				The path of the template to use
	 * @param	bool[optional] $customHeaders	Are custom headers already set?
	 */
	public function display($template, $customHeaders = false)
	{
		// do custom stuff
		$custom = new FrontendTemplateCustom($this);

		// parse constants
		$this->parseConstants();

		// check debug
		$this->parseDebug();

		// parse the label
		$this->parseLabels();

		// parse locale
		$this->parseLocale();

		// parse vars
		$this->parseVars();

		// parse headers
		if(!$customHeaders) SpoonHTTP::setHeaders('content-type: text/html;charset=utf-8');

		// call the parent
		parent::display($template);
	}


	/**
	 * Map the frontend-specific modifiers
	 *
	 * @return	void
	 */
	private function mapCustomModifiers()
	{
		// URL for a specific pageId
		$this->mapModifier('geturl', array('FrontendTemplateModifiers', 'getURL'));

		// URL for a specific block
		$this->mapModifier('geturlforblock', array('FrontendTemplateModifiers', 'getURLForBlock'));

		// convert var into navigation
		$this->mapModifier('getnavigation', array('FrontendTemplateModifiers', 'getNavigation'));

		// string
		$this->mapModifier('truncate', array('FrontendTemplateModifiers', 'truncate'));
		$this->mapModifier('cleanupplaintext', array('FrontendTemplateModifiers', 'cleanupPlainText'));

		// dates
		$this->mapModifier('timeago', array('FrontendTemplateModifiers', 'timeAgo'));

		// users
		$this->mapModifier('userSetting', array('FrontendTemplateModifiers', 'userSetting'));

		// debug stuff
		$this->mapModifier('dump', array('FrontendTemplateModifiers', 'dump'));
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

		// aliases
		$this->assign('LANGUAGE', FRONTEND_LANGUAGE);

		// settings
		$this->assign('SITE_TITLE', FrontendModel::getModuleSetting('core', 'site_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE));
	}


	/**
	 * Assigns an option if we are in debug-mode
	 *
	 * @return void
	 */
	private function parseDebug()
	{
		if(SPOON_DEBUG) $this->assign('debug', true);
	}


	/**
	 * Assign the labels
	 *
	 * @return	void
	 */
	private function parseLabels()
	{
		// assign actions
		$this->assignArray(FrontendLanguage::getActions(), 'act');

		// assign errors
		$this->assignArray(FrontendLanguage::getErrors(), 'err');

		// assign labels
		$this->assignArray(FrontendLanguage::getLabels(), 'lbl');

		// assign messages
		$this->assignArray(FrontendLanguage::getMessages(), 'msg');
	}


	/**
	 * Parse the locale (things like months, days, ...)
	 *
	 * @return	void
	 */
	private function parseLocale()
	{
		// init vars
		$locale = array();

		// get months
		$monthsLong = SpoonLocale::getMonths(FRONTEND_LANGUAGE, false);
		$monthsShort = SpoonLocale::getMonths(FRONTEND_LANGUAGE, true);

		// get days
		$daysLong = SpoonLocale::getWeekDays(FRONTEND_LANGUAGE, false, 'sunday');
		$daysShort = SpoonLocale::getWeekDays(FRONTEND_LANGUAGE, true, 'sunday');

		// build labels
		foreach($monthsLong as $key => $value) $locale['locMonthLong'. ucfirst($key)] = $value;
		foreach($monthsShort as $key => $value) $locale['locMonthShort'. ucfirst($key)] = $value;
		foreach($daysLong as $key => $value) $locale['locDayLong'. ucfirst($key)] = $value;
		foreach($daysShort as $key => $value) $locale['locDayShort'. ucfirst($key)] = $value;

		// assign
		$this->assignArray($locale);
	}


	/**
	 * Assign some default vars
	 *
	 * @return	void
	 */
	private function parseVars()
	{
		// assign a placeholder var
		$this->assign('var', '');

		// assign the current timestamp
		$this->assign('currentTimestamp', time());
	}
}


/**
 * FrontendTemplateMofidiers, contains all Frontend-related custom modifiers
 *
 * @package		frontend
 * @subpackage	template
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendTemplateModifiers
{
	/**
	 * Formats plain text as HTML, links will be detected, paragraphs will be inserted
	 *  syntax: {$var|cleanupPlainText}
	 *
	 * @return	string
	 * @param	string $var		The text to cleanup.
	 */
	public static function cleanupPlainText($var)
	{
		// redefine
		$var = (string) $var;

		// detect links
		$var = SpoonFilter::replaceURLsWithAnchors($var);

		// replace newlines
		$var = str_replace("\r", '', $var);
		$var = preg_replace('/(?<!.)(\r\n|\r|\n){3,}$/m', '', $var);

		// replace br's into p's
		$var = '<p>'. str_replace("\n", '</p><p>', $var) .'</p>';

		// cleanup
		$var = str_replace("\n", '', $var);
		$var = str_replace('<p></p>', '', $var);

		// return
		return $var;
	}


	/**
	 * Dumps the data
	 *  syntax: {$var|dump}
	 *
	 * @return	string
	 * @param	string $var		The variable to dump.
	 */
	public static function dump($var)
	{
		Spoon::dump($var, false);
	}


	/**
	 * Get the navigation html
	 * 	syntax: {$var|getnavigation[:<type>][:<parentId>][:<depth>][:<excludeIds-splitted-by-dash>]}
	 *
	 * @return	string
	 * @param	string[optional] $var
	 * @param	string[optional] $type			The type of navigation, possible values are: page, footer
	 * @param	int[optional] $parentId			The parent wherefore the navigation should be build
	 * @param	int[optional] $depth			The maximum depth that has to be build
	 * @param	string[optional] $excludeIds	Which pageIds should be excluded (split them by -)
	 */
	public static function getNavigation($var = null, $type = 'page', $parentId = 0, $depth = null, $excludeIds = null)
	{
		// build excludeIds
		if($excludeIds !== null) $excludeIds = (array) explode('-', $excludeIds);

		// get HTML
		$return = (string) FrontendNavigation::getNavigationHtml($type, $parentId, $depth, $excludeIds);

		// return the var
		if($return != '') return $return;

		// fallback
		return $var;
	}


	/**
	 * Get the URL for a given pageId & language
	 * 	syntax: {$var|geturl:404}
	 *
	 * @return	string
	 * @param	string $var
	 * @param	int $pageId						The id of the page to build the URL for.
	 * @param	string[optional] $language		The language to use, if not provided we will use the loaded language.
	 */
	public static function getURL($var, $pageId, $language = null)
	{
		return FrontendNavigation::getURL($pageId, $language);
	}


	/**
	 * Get the URL for a give module & action combination
	 * 	syntax: {$var|geturlforblock:<module>:<action>:<language>}
	 *
	 * @return	string
	 * @param	string $var
	 * @param	string $module					The module wherefor the URL should be build.
	 * @param	string[optional] $action		A specific action wherefor the URL should be build, otherwise the default will be used.
	 * @param	string[optional] $language		The language to use, if not provided we will use the loaded language.
	 */
	public static function getURLForBlock($var, $module, $action = null, $language = null)
	{
		return FrontendNavigation::getURLForBlock($module, $action, $language);
	}


	/**
	 * Formats a timestamp as a string that indicates the time ago
	 *  syntax: {$var|timeAgo}
	 *
	 * @return	string
	 * @param	string $var		A UNIX-timestamp that will be formated as a time-ago-string.
	 */
	public static function timeAgo($var = null)
	{
		// redefine
		$var = (int) $var;

		// invalid timestamp
		if($var == 0) return '';

		// return
		return '<abbr title="'. SpoonDate::getDate('d/m/Y H:i:s', $var, FRONTEND_LANGUAGE) .'">'. SpoonDate::getTimeAgo($var) .'</abbr>';
	}


	/**
	 * Truncate a string
	 *  syntax: {$var|truncate:<max-length>[:<append-hellip>]}
	 *
	 * @return	string
	 * @param	string $var
	 * @param	int $length					The maximum length of the truncated string.
	 * @param	bool[optional] $useHellip	Should a hellip be appended if the length exceeds the requested length?
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


	/**
	 * Get the value for a user-setting
	 *  syntax {$var|userSetting:<setting>[:<userId>]}
	 *
	 * @return	string
	 * @param	string $var
	 * @param	string $setting			The name of the setting you want.
	 * @param	int[optional] $userId	The userId, if not set by $var.
	 */
	public static function userSetting($var = null, $setting, $userId = null)
	{
		// redefine
		$userId = ($var !== null) ? (int) $var : (int) $userId;
		$setting = (string) $setting;

		// validate
		if($userId === 0) throw new FrontendException('Invalid userid');

		// get user
		$user = FrontendUser::getBackendUser($userId);

		// return setting
		return (string) $user->getSetting($setting);
	}
}

?>