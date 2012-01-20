<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our extended version of SpoonTemplate
 * This class will handle a lot of stuff for you, for example:
 * 	- it will assign all labels
 * 	- it will map some modifiers
 * 	- it will assign a lot of constants
 * 	- ...
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class FrontendTemplate extends SpoonTemplate
{
	/**
	 * Should we add slashes to each value?
	 *
	 * @var bool
	 */
	private $addSlashes = false;

	/**
	 * The constructor will store the instance in the reference, preset some settings and map the custom modifiers.
	 *
	 * @param bool[optional] $addToReference Should the instance be added into the reference.
	 */
	public function __construct($addToReference = true)
	{
		parent::__construct();

		if($addToReference) Spoon::set('template', $this);

		$this->setCacheDirectory(FRONTEND_CACHE_PATH . '/cached_templates');
		$this->setCompileDirectory(FRONTEND_CACHE_PATH . '/compiled_templates');
		$this->setForceCompile(SPOON_DEBUG);
		$this->mapCustomModifiers();
	}

	/**
	 * Compile a given template.
	 *
	 * @param string $path The path to the template, excluding the template filename.
	 * @param  string $template The filename of the template within the path.
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
	 * @param string $template The path of the template to use.
	 * @param bool[optional] $customHeaders Are custom headers already set?
	 * @param bool[optional] $parseCustom Parse custom template.
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
		if(!$customHeaders) SpoonHTTP::setHeaders('content-type: text/html;charset=' . SPOON_CHARSET);

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
	 * Retrives the already assigned variables.
	 *
	 * @return array
	 */
	public function getAssignedVariables()
	{
		return $this->variables;
	}

	/**
	 * Fetch the parsed content from this template.
	 *
	 * @param string $template The location of the template file, used to display this template.
	 * @param bool[optional] $customHeaders Are custom headers already set?
	 * @param bool[optional] $parseCustom Parse custom template.
	 * @return string The actual parsed content after executing this template.
	 */
	public function getContent($template, $customHeaders = false, $parseCustom = false)
	{
		ob_start();
		$this->display($template, $customHeaders, $parseCustom);
		return ob_get_clean();
	}

	/**
	 * Is the cache for this item still valid.
	 *
	 * @param string $name The name of the cached block.
	 * @return bool
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
		$this->mapModifier('camelcase', array('SpoonFilter', 'toCamelCase'));
		$this->mapModifier('stripnewlines', array('FrontendTemplateModifiers', 'stripNewlines'));

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
	 */
	private function parseDebug()
	{
		if(SPOON_DEBUG) $this->assign('debug', true);
	}

	/**
	 * Assign the labels
	 */
	private function parseLabels()
	{
		$actions = FrontendLanguage::getActions();
		$errors = FrontendLanguage::getErrors();
		$labels = FrontendLanguage::getLabels();
		$messages = FrontendLanguage::getMessages();

		// execute addslashes on the values for the locale, will be used in JS
		if($this->addSlashes)
		{
			foreach($actions as &$value)
			{
				if(!is_array($value)) $value = addslashes($value);
			}
			foreach($errors as &$value)
			{
				if(!is_array($value)) $value = addslashes($value);
			}
			foreach($labels as &$value)
			{
				if(!is_array($value)) $value = addslashes($value);
			}
			foreach($messages as &$value)
			{
				if(!is_array($value)) $value = addslashes($value);
			}
		}

		// assign actions
		$this->assignArray($actions, 'act');

		// assign errors
		$this->assignArray($errors, 'err');

		// assign labels
		$this->assignArray($labels, 'lbl');

		// assign messages
		$this->assignArray($messages, 'msg');
	}

	/**
	 * Parse the locale (things like months, days, ...)
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
		foreach($monthsLong as $key => $value) $locale['locMonthLong' . SpoonFilter::ucfirst($key)] = $value;
		foreach($monthsShort as $key => $value) $locale['locMonthShort' . SpoonFilter::ucfirst($key)] = $value;
		foreach($daysLong as $key => $value) $locale['locDayLong' . SpoonFilter::ucfirst($key)] = $value;
		foreach($daysShort as $key => $value) $locale['locDayShort' . SpoonFilter::ucfirst($key)] = $value;

		// assign
		$this->assignArray($locale);
	}

	/**
	 * Assign some default vars
	 */
	private function parseVars()
	{
		// assign a placeholder var
		$this->assign('var', '');

		// assign current timestamp
		$this->assign('timestamp', time());
	}

	/**
	 * Should we execute addSlashed on the locale?
	 *
	 * @param bool[optional] $on Enable addslashes.
	 */
	public function setAddSlashes($on = true)
	{
		$this->addSlashes = (bool) $on;
	}
}

/**
 * Contains all Frontend-related custom modifiers
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class FrontendTemplateModifiers
{
	/**
	 * Formats plain text as HTML, links will be detected, paragraphs will be inserted
	 * 	syntax: {$var|cleanupPlainText}
	 *
	 * @param string $var The text to cleanup.
	 * @return string
	 */
	public static function cleanupPlainText($var)
	{
		// redefine
		$var = (string) $var;

		// detect links
		$var = SpoonFilter::replaceURLsWithAnchors($var, FrontendModel::getModuleSetting('core', 'seo_nofollow_in_comments', false));

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
	 * @param string $var The variable to dump.
	 * @return string
	 */
	public static function dump($var)
	{
		Spoon::dump($var, false);
	}

	/**
	 * Format a number as currency
	 * 	syntax: {$var|formatcurrency[:currency[:decimals]]}
	 *
	 * @param string $var The string to form.
	 * @param string[optional] $currency The currency to will be used to format the number.
	 * @param int[optional] $decimals The number of decimals to show.
	 * @return string
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
	 * 	syntax: {$var|formatfloat[:decimals]}
	 *
	 * @param float $number The number to format.
	 * @param int[optional] $decimals The number of decimals.
	 * @return string
	 */
	public static function formatFloat($number, $decimals = 2)
	{
		return number_format((float) $number, (int) $decimals, '.', ' ');
	}

	/**
	 * Format a number
	 * 	syntax: {$var|formatnumber}
	 *
	 * @param float $var The number to format.
	 * @return string
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
	 * 	syntax: {$var|getnavigation[:type[:parentId[:depth[:excludeIds-splitted-by-dash]]]]}
	 *
	 * @param string[optional] $var The variable.
	 * @param string[optional] $type The type of navigation, possible values are: page, footer.
	 * @param int[optional] $parentId The parent wherefore the navigation should be build.
	 * @param int[optional] $depth The maximum depth that has to be build.
	 * @param string[optional] $excludeIds Which pageIds should be excluded (split them by -).
	 * @return string
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
	 * 	syntax: {$var|getpageinfo:pageId[:field[:language]]}
	 *
	 * @param string[optional] $var The string passed from the template.
	 * @param int $pageId The id of the page to build the URL for.
	 * @param string[optional] $field The field to get.
	 * @param string[optional] $language The language to use, if not provided we will use the loaded language.
	 * @return string
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
	 * 	syntax: {$var|getpath:file}
	 *
	 * @param string $var The variable.
	 * @param string $file The base path.
	 * @return string
	 */
	public static function getPath($var, $file)
	{
		// trick codensiffer
		$var = (string) $var;

		return FrontendTheme::getPath($file);
	}

	/**
	 * Get the subnavigation html
	 * 	syntax: {$var|getsubnavigation[:type[:parentId[:startdepth[:enddepth[:'excludeIds-splitted-by-dash']]]]]}
	 *
	 * 	NOTE: When supplying more than 1 ID to exclude, the single quotes around the dash-separated list are mandatory.
	 *
	 * @param string[optional] $var The variable.
	 * @param string[optional] $type The type of navigation, possible values are: page, footer.
	 * @param int[optional] $pageId The parent wherefore the navigation should be build.
	 * @param int[optional] $startDepth The depth to strat from.
	 * @param int[optional] $endDepth The maximum depth that has to be build.
	 * @param string[optional] $excludeIds Which pageIds should be excluded (split them by -).
	 * @return string
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
	 * 	syntax: {$var|geturl:pageId[:language]}
	 *
	 * @param string $var The string passed from the template.
	 * @param int $pageId The id of the page to build the URL for.
	 * @param string[optional] $language The language to use, if not provided we will use the loaded language.
	 * @return string
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
	 * 	syntax: {$var|geturlforblock:module[:action[:language]]}
	 *
	 * @param string $var The string passed from the template.
	 * @param string $module The module wherefor the URL should be build.
	 * @param string[optional] $action A specific action wherefor the URL should be build, otherwise the default will be used.
	 * @param string[optional] $language The language to use, if not provided we will use the loaded language.
	 * @return string
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
	 * 	syntax: {$var|geturlforextraid:extraId[:language]}
	 *
	 * @param string $var The string passed from the template.
	 * @param int $extraId The id of the extra.
	 * @param string[optional] $language The language to use, if not provided we will use the loaded language.
	 * @return string
	 */
	public static function getURLForExtraId($var, $extraId, $language = null)
	{
		$var = (string) $var;
		$extraId = (int) $extraId;
		$language = ($language !== null) ? (string) $language : null;

		// return url
		return FrontendNavigation::getURLForExtraId($extraId, $language);
	}

	/**
	 * Highlights all strings in <code> tags.
	 * 	syntax: {$var|highlight}
	 *
	 * @param string $var The string passed from the template.
	 * @return string
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

		return $var;
	}

	/**
	 * Get a random var between a min and max
	 * 	syntax: {$var|rand:min:max}
	 *
	 * @param string[optional] $var The string passed from the template.
	 * @param int $min The miminum random number.
	 * @param int $max The maximum random number.
	 * @return int
	 */
	public static function random($var = null, $min, $max)
	{
		$var = (string) $var;
		$min = (int) $min;
		$max = (int) $max;

		return rand($min, $max);
	}

	/**
	 * Convert a multiline string into a string without newlines so it can be handles by JS
	 * syntax: {$var|stripnewlines}
	 *
	 * @param string $var The variable that should be processed.
	 * @return string
	 */
	public static function stripNewlines($var)
	{
		return str_replace(array("\n", "\r"), '', $var);
	}

	/**
	 * Formats a timestamp as a string that indicates the time ago
	 * 	syntax: {$var|timeago}
	 *
	 * @param string[optional] $var A UNIX-timestamp that will be formated as a time-ago-string.
	 * @return string
	 */
	public static function timeAgo($var = null)
	{
		$var = (int) $var;

		// invalid timestamp
		if($var == 0) return '';

		// return
		return '<abbr title="' . SpoonDate::getDate(FrontendModel::getModuleSetting('core', 'date_format_long') . ', ' . FrontendModel::getModuleSetting('core', 'time_format'), $var, FRONTEND_LANGUAGE) . '">' . SpoonDate::getTimeAgo($var, FRONTEND_LANGUAGE) . '</abbr>';
	}

	/**
	 * Truncate a string
	 * 	syntax: {$var|truncate:max-length[:append-hellip]}
	 *
	 * @param string[optional] $var The string passed from the template.
	 * @param int $length The maximum length of the truncated string.
	 * @param bool[optional] $useHellip Should a hellip be appended if the length exceeds the requested length?
	 * @return string
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
			$var = mb_substr($var, 0, $length, SPOON_CHARSET);

			// add hellip
			if($useHellip) $var .= '…';

			// return
			return SpoonFilter::htmlspecialchars($var, ENT_QUOTES);
		}
	}

	/**
	 * Get the value for a user-setting
	 * 	syntax {$var|usersetting:setting[:userId]}
	 *
	 * @param string[optional] $var The string passed from the template.
	 * @param string $setting The name of the setting you want.
	 * @param int[optional] $userId The userId, if not set by $var.
	 * @return string
	 */
	public static function userSetting($var = null, $setting, $userId = null)
	{
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
