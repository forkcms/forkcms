<?php

/**
 * This is our extended version of SpoonTemplate
 * This class will handle a lot of stuff for you, for example:
 * 	- it will assign all labels
 * 	- it will map some modifiers
 * 	- it will assign a lot of constants
 * 	- ...
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @since		2.0
 */
class FrontendTemplate extends SpoonTemplate
{
	/**
	 * Class constructor
	 * The constructor will store the instance in the reference, preset some settings and map the custom modifiers.
	 *
	 * @return	void
	 * @param	bool[optional] $addToReference	Should the instance be added into the reference.
	 */
	public function __construct($addToReference = true)
	{
		// parent constructor
		parent::__construct();

		// store in reference so we can access it from everywhere
		if($addToReference) Spoon::set('template', $this);

		// set cache directory
		$this->setCacheDirectory(FRONTEND_CACHE_PATH . '/cached_templates');

		// set compile directory
		$this->setCompileDirectory(FRONTEND_CACHE_PATH . '/compiled_templates');

		// when debugging the template should be recompiled every time
		$this->setForceCompile(SPOON_DEBUG);

		// map custom modifiers
		$this->mapCustomModifiers();
	}


	/**
	 * Compile a given template.
	 *
	 * @return	void
	 * @param	string $path		The path to the template, excluding the template filename.
	 * @param 	string $template	The filename of the template within the path.
	 */
	public function compile($path, $template)
	{
		// redefine template
		if(realpath($template) === false) $template = $path . '/' . $template;

		// source file does not exist
		if(!SpoonFile::exists($template)) return false;

		// create object
		$compiler = new FrontendTemplateCompiler($template, $this->variables);

		// set some options
		$compiler->setCacheDirectory($this->cacheDirectory);
		$compiler->setCompileDirectory($this->compileDirectory);
		$compiler->setForceCompile($this->forceCompile);
		$compiler->setForms($this->forms);

		// compile & save
		$compiler->parseToFile();

		// status
		return true;
	}


	/**
	 * Output the template into the browser
	 * Will also assign the labels and all user-defined constants.
	 * If you want custom-headers, you should set them yourself, otherwise the content-type and charset will be set
	 *
	 * @return	void
	 * @param	string $template				The path of the template to use.
	 * @param	bool[optional] $customHeaders	Are custom headers already set?
	 * @param	bool[optional] $parseCustom		Parse custom template.
	 */
	public function display($template, $customHeaders = false, $parseCustom = false)
	{
		// do custom stuff
		if($parseCustom) new FrontendTemplateCustom($this);

		// parse constants
		$this->parseConstants();

		// check debug
		$this->parseDebug();

		// parse the label
		$this->parseLabels();

		// parse locale
		$this->parseLocale();

		// parse date/time formats
		$this->parseDateTimeFormats();

		// parse vars
		$this->parseVars();

		// parse headers
		if(!$customHeaders) SpoonHTTP::setHeaders('content-type: text/html;charset=utf-8');

		// get template path
		$template = FrontendTheme::getPath($template);

		/*
		 * Code below is exactly the same as from our parent (SpoonTemplate::display), exept
		 * for the compiler being used. We want our own compiler extension here.
		 */

		// redefine
		$template = (string) $template;

		// validate name
		if(trim($template) == '' || !SpoonFile::exists($template)) throw new SpoonTemplateException('Please provide an existing template.');

		// compiled name
		$compileName = $this->getCompileName((string) $template);

		// compiled if needed
		if($this->forceCompile || !SpoonFile::exists($this->compileDirectory . '/' . $compileName))
		{
			// create compiler
			$compiler = new FrontendTemplateCompiler((string) $template, $this->variables);

			// set some options
			$compiler->setCacheDirectory($this->cacheDirectory);
			$compiler->setCompileDirectory($this->compileDirectory);
			$compiler->setForceCompile($this->forceCompile);
			$compiler->setForms($this->forms);

			// compile & save
			$compiler->parseToFile();
		}

		// load template
		require $this->compileDirectory . '/' . $compileName;
	}


	/**
	 * Fetch the parsed content from this template.
	 *
	 * @return	string							The actual parsed content after executing this template.
	 * @param	string $template				The location of the template file, used to display this template.
	 * @param	bool[optional] $customHeaders	Are custom headers already set?
	 * @param	bool[optional] $parseCustom		Parse custom template.
	 */
	public function getContent($template, $customHeaders = false, $parseCustom = false)
	{
		// turn on output buffering
		ob_start();

		// show output
		$this->display($template, $customHeaders, $parseCustom);

		// return template content
		return ob_get_clean();
	}


	/**
	 * Is the cache for this item still valid.
	 *
	 * @return	bool			Is this template block cached?
	 * @param	string $name	The name of the cached block.
	 */
	public function isCached($name)
	{
		// never cached in debug
		if(SPOON_DEBUG) return false;

		// let parent do the actual check
		else return parent::isCached($name);
	}


	/**
	 * Map the frontend-specific modifiers
	 *
	 * @return	void
	 */
	private function mapCustomModifiers()
	{
		// fetch the path for an include (theme file if available, core file otherwise)
		$this->mapModifier('getpath', array('FrontendTemplateModifiers', 'getPath'));

		// formatting
		$this->mapModifier('formatcurrency', array('FrontendTemplateModifiers', 'formatCurrency'));

		// URL for a specific pageId
		$this->mapModifier('geturl', array('FrontendTemplateModifiers', 'getURL'));

		// URL for a specific block/extra
		$this->mapModifier('geturlforblock', array('FrontendTemplateModifiers', 'getURLForBlock'));
		$this->mapModifier('geturlforextraid', array('FrontendTemplateModifiers', 'getURLForExtraId'));

		// page related
		$this->mapModifier('getpageinfo', array('FrontendTemplateModifiers', 'getPageInfo'));

		// convert var into navigation
		$this->mapModifier('getnavigation', array('FrontendTemplateModifiers', 'getNavigation'));
		$this->mapModifier('getsubnavigation', array('FrontendTemplateModifiers', 'getSubNavigation'));

		// rand
		$this->mapModifier('rand', array('FrontendTemplateModifiers', 'random'));

		// string
		$this->mapModifier('formatfloat', array('FrontendTemplateModifiers', 'formatFloat'));
		$this->mapModifier('formatnumber', array('FrontendTemplateModifiers', 'formatNumber'));
		$this->mapModifier('truncate', array('FrontendTemplateModifiers', 'truncate'));
		$this->mapModifier('cleanupplaintext', array('FrontendTemplateModifiers', 'cleanupPlainText'));

		// dates
		$this->mapModifier('timeago', array('FrontendTemplateModifiers', 'timeAgo'));

		// users
		$this->mapModifier('usersetting', array('FrontendTemplateModifiers', 'userSetting'));

		// highlight
		$this->mapModifier('highlight', array('FrontendTemplateModifiers', 'highlightCode'));

		// urlencode
		$this->mapModifier('urlencode', 'urlencode');

		// strip tags
		$this->mapModifier('striptags', 'strip_tags');

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
		$this->assign('is' . strtoupper(FRONTEND_LANGUAGE), true);

		// settings
		$this->assign('SITE_TITLE', FrontendModel::getModuleSetting('core', 'site_title_' . FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE));

		// facebook stuff
		if(FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null) !== null) $this->assign('FACEBOOK_ADMIN_IDS', FrontendModel::getModuleSetting('core', 'facebook_admin_ids', null));
		if(FrontendModel::getModuleSetting('core', 'facebook_app_id', null) !== null) $this->assign('FACEBOOK_APP_ID', FrontendModel::getModuleSetting('core', 'facebook_app_id', null));
		if(FrontendModel::getModuleSetting('core', 'facebook_app_secret', null) !== null) $this->assign('FACEBOOK_APP_SECRET', FrontendModel::getModuleSetting('core', 'facebook_app_secret', null));
		if(FrontendModel::getModuleSetting('core', 'facebook_api_key', null) !== null) $this->assign('FACEBOOK_API_KEY', FrontendModel::getModuleSetting('core', 'facebook_api_key', null));

		// theme
		if(FrontendModel::getModuleSetting('core', 'theme') !== null)
		{
			$this->assign('THEME', FrontendModel::getModuleSetting('core', 'theme', 'default'));
			$this->assign('THEME_PATH', FRONTEND_PATH . '/themes/' . FrontendModel::getModuleSetting('core', 'theme', 'default'));
			$this->assign('THEME_URL', '/frontend/themes/' . FrontendModel::getModuleSetting('core', 'theme', 'default'));
		}
	}


	/**
	 * Parses the general date and time formats
	 *
	 * @return	void
	 */
	private function parseDateTimeFormats()
	{
		// time format
		$this->assign('timeFormat', FrontendModel::getModuleSetting('core', 'time_format'));

		// date formats (short & long)
		$this->assign('dateFormatShort', FrontendModel::getModuleSetting('core', 'date_format_short'));
		$this->assign('dateFormatLong', FrontendModel::getModuleSetting('core', 'date_format_long'));
	}


	/**
	 * Assigns an option if we are in debug-mode
	 *
	 * @return	void
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
		foreach($monthsLong as $key => $value) $locale['locMonthLong' . ucfirst($key)] = $value;
		foreach($monthsShort as $key => $value) $locale['locMonthShort' . ucfirst($key)] = $value;
		foreach($daysLong as $key => $value) $locale['locDayLong' . ucfirst($key)] = $value;
		foreach($daysShort as $key => $value) $locale['locDayShort' . ucfirst($key)] = $value;

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

		// assign current timestamp
		$this->assign('timestamp', time());
	}
}


/**
 * Contains all Frontend-related custom modifiers
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class FrontendTemplateModifiers
{
	/**
	 * Formats plain text as HTML, links will be detected, paragraphs will be inserted
	 * 	syntax: {$var|cleanupPlainText}
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
		$var = '<p>' . str_replace("\n", '</p><p>', $var) . '</p>';

		// cleanup
		$var = str_replace("\n", '', $var);
		$var = str_replace('<p></p>', '', $var);

		// return
		return $var;
	}


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
	 * Format a number as currency
	 * 	syntax: {$var|formatcurrency[:<currency>][:<decimals>]}
	 *
	 * @return	string
	 * @param	string $var						The string to form.
	 * @param	string[optional] $currency		The currency to will be used to format the number.
	 * @param	int[optional] $decimals			The number of decimals to show.
	 */
	public static function formatCurrency($var, $currency = 'EUR', $decimals = null)
	{
		// @later get settings from backend
		switch($currency)
		{
			case 'EUR':
				$decimals = ($decimals === null) ? 2 : (int) $decimals;

				// format as Euro
				return '€ ' . number_format((float) $var, $decimals, ',', ' ');
			break;
		}
	}


	/**
	 * Format a number as a float
	 * @later	grab settings from database
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

		return number_format($number, $decimals, '.', ' ');
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
		$format = FrontendModel::getModuleSetting('core', 'number_format');

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
	 * Get the navigation html
	 * 	syntax: {$var|getnavigation[:<type>][:<parentId>][:<depth>][:<excludeIds-splitted-by-dash>]}
	 *
	 * @return	string
	 * @param	string[optional] $var			The variable.
	 * @param	string[optional] $type			The type of navigation, possible values are: page, footer.
	 * @param	int[optional] $parentId			The parent wherefore the navigation should be build.
	 * @param	int[optional] $depth			The maximum depth that has to be build.
	 * @param	string[optional] $excludeIds	Which pageIds should be excluded (split them by -).
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
	 * Get a given field for a page-record
	 * 	syntax: {$var|getpageinfo:404:'title'}
	 *
	 * @return	string
	 * @param	string[optional] $var			The string passed from the template.
	 * @param	int $pageId						The id of the page to build the URL for.
	 * @param	string[optional] $field			The field to get.
	 * @param	string[optional] $language		The language to use, if not provided we will use the loaded language.
	 */
	public static function getPageInfo($var = null, $pageId, $field = 'title', $language = null)
	{
		// redefine
		$var = (string) $var;
		$pageId = (int) $pageId;
		$field = (string) $field;
		$language = ($language !== null) ? (string) $language : null;

		// get page
		$page = FrontendNavigation::getPageInfo($pageId);

		// validate
		if(empty($page)) return '';
		if(!isset($page[$field])) return '';

		// return page info
		return $page[$field];
	}


	/**
	 * Fetch the path for an include (theme file if available, core file otherwise)
	 *
	 * @return	string
	 * @param	string $var		The variable.
	 * @param	string $file	The base path.
	 */
	public static function getPath($var, $file)
	{
		// trick codensiffer
		$var = (string) $var;

		return FrontendTheme::getPath($file);
	}


	/**
	 * Get the subnavigation html
	 * 	syntax: {$var|getsubnavigation[:<type>][:<parentId>][:<startdepth>][:<enddepth>][:<excludeIds-splitted-by-dash>]}
	 *
	 * @return	string
	 * @param	string[optional] $var			The variable.
	 * @param	string[optional] $type			The type of navigation, possible values are: page, footer.
	 * @param	int[optional] $pageId			The parent wherefore the navigation should be build.
	 * @param	int[optional] $startDepth		The depth to strat from.
	 * @param	int[optional] $endDepth			The maximum depth that has to be build.
	 * @param	string[optional] $excludeIds	Which pageIds should be excluded (split them by -).
	 */
	public static function getSubNavigation($var = null, $type = 'page', $pageId = 0, $startDepth = 1, $endDepth = null, $excludeIds = null)
	{
		// build excludeIds
		if($excludeIds !== null) $excludeIds = (array) explode('-', $excludeIds);

		// get info about the given page
		$pageInfo = FrontendNavigation::getPageInfo($pageId);

		// validate page info
		if($pageInfo === false) return '';

		// split URL into chunks
		$chunks = (array) explode('/', $pageInfo['full_url']);

		// init var
		$parentURL = '';

		// build url
		for($i = 0; $i < $startDepth - 1; $i++) $parentURL .= $chunks[$i] . '/';

		// get parent ID
		$parentID = FrontendNavigation::getPageId($parentURL);

		try
		{
			// get HTML
			$return = (string) FrontendNavigation::getNavigationHtml($type, $parentID, $endDepth, $excludeIds);
		}

		// catch exceptions
		catch(Exception $e)
		{
			return '';
		}

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
	 * @param	string $var						The string passed from the template.
	 * @param	int $pageId						The id of the page to build the URL for.
	 * @param	string[optional] $language		The language to use, if not provided we will use the loaded language.
	 */
	public static function getURL($var, $pageId, $language = null)
	{
		// redefine
		$var = (string) $var;
		$pageId = (int) $pageId;
		$language = ($language !== null) ? (string) $language : null;

		// return url
		return FrontendNavigation::getURL($pageId, $language);
	}


	/**
	 * Get the URL for a give module & action combination
	 * 	syntax: {$var|geturlforblock:<module>:<action>:<language>}
	 *
	 * @return	string
	 * @param	string $var						The string passed from the template.
	 * @param	string $module					The module wherefor the URL should be build.
	 * @param	string[optional] $action		A specific action wherefor the URL should be build, otherwise the default will be used.
	 * @param	string[optional] $language		The language to use, if not provided we will use the loaded language.
	 */
	public static function getURLForBlock($var, $module, $action = null, $language = null)
	{
		// redefine
		$var = (string) $var;
		$module = (string) $module;
		$action = ($action !== null) ? (string) $action : null;
		$language = ($language !== null) ? (string) $language : null;

		// return url
		return FrontendNavigation::getURLForBlock($module, $action, $language);
	}


	/**
	 * Fetch an URL based on an extraId
	 *
	 * @return	string
	 * @param	string $var						The string passed from the template.
	 * @param	int $extraId					The id of the extra.
	 * @param	string[optional] $language		The language to use, if not provided we will use the loaded language.
	 */
	public static function getURLForExtraId($var, $extraId, $language = null)
	{
		// redefine
		$var = (string) $var;
		$extraId = (int) $extraId;
		$language = ($language !== null) ? (string) $language : null;

		// return url
		return FrontendNavigation::getURLForExtraId($extraId, $language);
	}


	/**
	 * Highlights all strings in <code> tags.
	 *
	 * @return	string
	 * @param	string $var		The string passed from the template.
	 */
	public static function highlightCode($var)
	{
		// regex pattern
		$pattern = '/<code>.*?<\/code>/is';

		// find matches
		if(preg_match_all($pattern, $var, $matches))
		{
			// loop matches
			foreach($matches[0] as $match)
			{
				// encase content in highlight_string
				$content = str_replace($match, highlight_string($match, true), $var);

				// replace highlighted code tags in match
				$content = str_replace(array('&lt;code&gt;', '&lt;/code&gt;'), '', $var);
			}
		}

		// return content
		return $var;
	}


	/**
	 * Get a random var between a min and max
	 *
	 * @return	int
	 * @param	string[optional] $var	The string passed from the template.
	 * @param	int $min				The miminum random number.
	 * @param	int $max				The maximum random number.
	 */
	public static function random($var = null, $min, $max)
	{
		// redefine
		$var = (string) $var;
		$min = (int) $min;
		$max = (int) $max;

		return rand($min, $max);
	}


	/**
	 * Formats a timestamp as a string that indicates the time ago
	 * 	syntax: {$var|timeAgo}
	 *
	 * @return	string
	 * @param	string[optional] $var		A UNIX-timestamp that will be formated as a time-ago-string.
	 */
	public static function timeAgo($var = null)
	{
		// redefine
		$var = (int) $var;

		// invalid timestamp
		if($var == 0) return '';

		// return
		return '<abbr title="' . SpoonDate::getDate(FrontendModel::getModuleSetting('core', 'date_format_long') . ', ' . FrontendModel::getModuleSetting('core', 'time_format'), $var, FRONTEND_LANGUAGE) . '">' . SpoonDate::getTimeAgo($var, FRONTEND_LANGUAGE) . '</abbr>';
	}


	/**
	 * Truncate a string
	 * 	syntax: {$var|truncate:<max-length>[:<append-hellip>]}
	 *
	 * @return	string
	 * @param	string[optional] $var		The string passed from the template.
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


	/**
	 * Get the value for a user-setting
	 * 	syntax {$var|usersetting:<setting>[:<userId>]}
	 *
	 * @return	string
	 * @param	string[optional] $var	The string passed from the template.
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

		// return
		return (string) $user->getSetting($setting);
	}
}

?>