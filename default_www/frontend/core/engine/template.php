<?php

/**
 * FrontendTemplate, this is our extended version of SpoonTemplate
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
	 */
	public function __construct()
	{
		// store in reference so we can access it from everywhere
		Spoon::setObjectReference('template', $this);

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
	 * Will also assign the interfacelabels and all user-defined constants.
	 *
	 * @return	void
	 * @param	string $template
	 * @param	bool[optional] $customHeaders
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

		// assign a placeholder var
		$this->assign('var', '');

		// assign the current timestamp
		$this->assign('currentTimestamp', time());

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
		$this->mapModifier('geturl', array('FrontendTemplateModifiers', 'getURL'));
		$this->mapModifier('getURL', array('FrontendTemplateModifiers', 'getURL'));

		// convert var into navigation, syntax {$var|getnavigation[:<start-depth>][:<end-depth>]}
		$this->mapModifier('getnavigation', array('FrontendTemplateModifiers', 'getNavigation'));
		$this->mapModifier('getNavigation', array('FrontendTemplateModifiers', 'getNavigation'));

		// convert var into a title, syntax {$var|gettitle:<pageId>}
		$this->mapModifier('gettitle', array('FrontendTemplateModifiers', 'getTitle'));
		$this->mapModifier('getTitle', array('FrontendTemplateModifiers', 'getTitle'));

		// string
		$this->mapModifier('truncate', array('FrontendTemplateModifiers', 'truncate'));

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
		// get all actions
		$actions = FrontendLanguage::getActions();

		// get all errors
		$errors = FrontendLanguage::getErrors();

		// get all labels
		$labels = FrontendLanguage::getLabels();

		// get all messages
		$messages = FrontendLanguage::getMessages();

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
	 *
	 * @return	void
	 */
	private function parseLocale()
	{
		// init vars
		$localeToAssign = array();

		// get months
		$monthsLong = SpoonLocale::getMonths(FRONTEND_LANGUAGE, false);
		$monthsShort = SpoonLocale::getMonths(FRONTEND_LANGUAGE, true);

		// get days
		$daysLong = SpoonLocale::getWeekDays(FRONTEND_LANGUAGE, false, 'sunday');
		$daysShort = SpoonLocale::getWeekDays(FRONTEND_LANGUAGE, true, 'sunday');

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
 * FrontendTemplateMofidiers, contains all Fork-related modifiers
 *
 * This source file is part of Fork CMS.
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
	 * @param	string $action
	 * @param	string[optional] $module
	 */
	public static function getURL($var = null, $action = null, $module = null)
	{
		return FrontendModel::createURLForAction($action, $module, FRONTEND_LANGUAGE);
	}


	/**
	 * Get the navigation html
	 * 	syntax: {$var|getnavigation[:<type>][:<parentId>][:<depth>][:<excludeIds-splitted-by-dash>]}
	 *
	 * @return	string
	 * @param	string[optional] $var
	 * @param	string[optional] $type
	 * @param	int[optional] $startDepth
	 * @param	int[optional] $endDepth
	 * @param	string[optional] $excludeIds
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
	 * Convert a var into a certain pagetitle
	 * 	syntax: {$var|gettitle:<pageId>}
	 *
	 * @return	void
	 * @param	string[optional] $var
	 * @param	int $pageId
	 */
	public static function getTitle($var = null, $pageId)
	{
		// get info
		$pageInfo = FrontendNavigation::getPageInfo($pageId);

		// return the title
		if($pageInfo !== false && isset($pageInfo['navigation'])) return $pageInfo['navigation'];

		// fallback
		return $var;
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


/**
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	template
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendTemplateCustom
{
	/**
	 * Template instance
	 *
	 * @var	ForkTemplate
	 */
	private $tpl;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	ForkTemplate $tpl
	 */
	public function __construct($tpl)
	{
		// set property
		$this->tpl = $tpl;

		// call parse
		$this->parse();
	}


	/**
	 * Parse the custom stuff
	 *
	 * @return	void
	 */
	private function parse()
	{
		// insert your custom stuff here...
	}

}

?>