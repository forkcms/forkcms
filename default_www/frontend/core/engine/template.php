<?php

// require SpoonTemplate
require_once 'spoon/template/template.php';

/**
 * ForkTemplate, this is our extended version of SpoonTemplate
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
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// set cache directory
		$this->setCacheDirectory(FRONTEND_CACHE_PATH .'/templates');

		// set compile directory
		$this->setCompileDirectory(FRONTEND_CACHE_PATH .'/templates');

		// when debugging the template should be recompiled every time
		$this->setForceCompile(SPOON_DEBUG);

		// map custom modifiers
		$this->mapCustomModifiers();
	}


	/**
	 * Display the page
	 *
	 * @return	void
	 * @param	string $name
	 */
	public function display($name)
	{
		// parse the label
		$this->parseLabels();

		// parse constants
		$this->parseConstants();

		// do custom stuff
		$custom = new FrontendTemplateCustom($this);

		// call the parent
		parent::display($name);
	}


	/**
	 * Map the fork-specific modifiers
	 *
	 * @return	void
	 */
	private function mapCustomModifiers()
	{
		// convert vars into an url, syntax {$var|geturl:<pageId>}
		$this->mapModifier('geturl', array('ForkTemplateModifiers', 'getURL'));

		// convert vars into an url, syntax {$var|gettitle:<pageId>}
		$this->mapModifier('gettitle', array('ForkTemplateModifiers', 'getTitle'));

		// convert vars into an url, syntax {$var|getnavigation[:<start-depth>][:<end-depth>]}
		$this->mapModifier('getnavigation', array('ForkTemplateModifiers', 'getNavigation'));
	}


	/**
	 * Parse all user-defined constants
	 *
	 * @return	void
	 */
	private function parseConstants()
	{
		// constants that should be protected from usage in the template
		$secretConstants = array('DB_TYPE', 'DB_DATABASE', 'DB_HOSTNAME', 'DB_USERNAME', 'DB_PASSWORD');

		// get all defined constants
		$constants = get_defined_constants(true);

		// unset protected constants
		foreach($secretConstants as $constant) if(isset($constants['user'][$constant])) unset($constants['user'][$constant]);

		// if our constants are there assign them
		if(isset($constants['user'])) $this->assign($constants['user']);

		// aliases
		$this->assign('LANGUAGE', FRONTEND_LANGUAGE);

		// settings
		$this->assign('SITE_TITLE', FrontendModel::getModuleSetting('core', 'site_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE));
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
}


/**
 * ForkTemplateMofidiers, contains all Fork-related modifiers
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	template
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class ForkTemplateModifiers
{
	/**
	 * Get the navigation html
	 * 	syntax: {$var|getnavigation[:<pageid>][:<startdepth>][:<enddepth>][:<excludeIds>]}
	 *
	 * @return	string
	 * @param	string[optional] $var
	 * @param	int[optional] $pageId
	 * @param	int[optional] $startDepth
	 * @param	int[optional] $endDepth
	 * @param	array[optional] $excludeIds
	 */
	public static function getNavigation($var = null, $pageId = 0, $startDepth = 1, $endDepth = null, $excludeIds = null)
	{
		// get HTML
		$return = (string) FrontendNavigation::getNavigationHtml($pageId, $startDepth, $endDepth, $excludeIds);

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
	 * Convert a var into a url
	 * 	syntax: {$var|geturl:<pageId>}
	 *
	 * @return	void
	 * @param	string[optional] $var
	 * @param	int $pageId
	 */
	public static function getURL($var = null, $pageId)
	{
		return (string) FrontendNavigation::getUrlByPageId($pageId);
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