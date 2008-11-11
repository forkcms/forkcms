<?php
/** Require SpoonTemplate */
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
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class ForkTemplate extends SpoonTemplate
{
	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
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


	private function mapCustomModifiers()
	{
		// convert vars into an url, syntax {$var|getUrl:<pageId>}
		$this->mapModifier('geturl', array('ForkTemplateModifiers', 'getUrl'));

		// convert vars into an url, syntax {$var|getTitle:<pageId>}
		$this->mapModifier('gettitle', array('ForkTemplateModifiers', 'getTitle'));
	}


	/**
	 * Parse all user-defined constants
	 *
	 * @return	void
	 */
	private function parseConstants()
	{
		// get all defined constants
		$aConstants = get_defined_constants(true);

		// if our constants are there assign them
		if(isset($aConstants['user'])) $this->assign($aConstants['user']);

		// aliases
		$this->assign('LANGUAGE', FRONTEND_LANGUAGE);

		// settings
		$this->assign('SITE_TITLE', CoreModel::getModuleSetting('core', 'site_title_'. FRONTEND_LANGUAGE, SITE_DEFAULT_TITLE));
	}


	/**
	 * Assign the labels
	 *
	 * @return	void
	 */
	private function parseLabels()
	{
		$this->assignArray(FrontendLanguage::getActions(), 'act');
		$this->assignArray(FrontendLanguage::getErrors(), 'err');
		$this->assignArray(FrontendLanguage::getLabels(), 'lbl');
		$this->assignArray(FrontendLanguage::getMessages(), 'msg');
	}
}

/**
 * ForkTemplateMofidiers, contains all Fork-related modifiers
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class ForkTemplateModifiers
{
	/**
	 * Convert a var into a url
	 * 	syntax: {$var|geturl:<pageId>}
	 *
	 * @return	void
	 * @param	string[optional] $var
	 * @param	int $pageId
	 */
	public static function getUrl($var = null, $pageId)
	{
		return (string) FrontendNavigation::getUrlByPageId($pageId);
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
}
?>