<?php

/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		frontend
 * @subpackage	navigation
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendNavigation extends FrontendBaseObject
{
	/**
	 * The keys
	 *
	 * @var	array
	 */
	private static	$aKeys = array(),
					$aNavigation = array();


	/**
	 * The pageids for the selected pages
	 *
	 * @var	array
	 */
	private static $aSelectedPageIds = array();


	/**
	 * Default constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// call the parent
		parent::__construct();

		// set selected ids
		$this->setSelectedPageIds();
	}


	/**
	 * Creates the html for the menu
	 *
	 * @return	string
	 * @param	int[optional] $parentId
	 * @param	int[optional] $startDepth
	 * @param	int[optional] $maxDepth
	 * @param	array[optional] $excludedIds
	 * @param	string[optional] $html
	 */
	private static function createHtml($parentId = 0, $startDepth = 1, $endDepth = null, $excludedIds = array(), $html = '')
	{
		// init vars
		$defaultSelectedClass = 'selected';

		// validation
		if($endDepth != null && $endDepth < $startDepth) return $html;

		$aNavigation = self::getNavigation();

		// check if item exists
		if(isset($aNavigation[$startDepth][$parentId]))
		{
			// start html
			$html .= '<ul>' . "\n";

			// loop elements
			foreach ($aNavigation[$startDepth][$parentId] as $key => $aValue)
			{
				// check if elements should be excluded
				if(!in_array($key, (array) $excludedIds))
				{
					// check if this item should be selected
					$selected = (in_array($key, self::$aSelectedPageIds));
					$class = ($selected) ? $defaultSelectedClass : '';

					// add html
					if($class != null) $html .= "\t<li class=\"". $class ."\">" . "\n";
					else $html .= "\t<li>" . "\n";

					$html .= "\t\t". '<a href="'. self::getUrlByPageId($key) .'" title="'. $aValue['navigation'] .'">'. $aValue['navigation'] .'</a>' . "\n";

					// insert recursive here!
					if(isset($aNavigation[$startDepth + 1][$key]) && $selected) $html .= self::createHtml($key, $startDepth + 1, $endDepth, $excludedIds, '');

					// add html
					$html .= '</li>' . "\n";
				}
			}

			// end html
			$html .= '</ul>' . "\n";
		}

		// return
		return $html;
	}


	public static function getFirstChildIdByPageId($pageId)
	{
		// redefine
		$pageId = (int) $pageId;

		// init var
		$aNavigation = self::getNavigation();

		// loop depths
		foreach($aNavigation as $depth => $aParent)
		{
			// first check
			if(!isset($aParent[$pageId])) continue;

			// get keys
			$aKeys = array_keys($aParent[$pageId]);

			// get first item
			if(isset($aKeys[0])) return $aKeys[0];
		}

		// fallback
		return false;
	}


	/**
	 * Get all footerlinks
	 *
	 * @return	array
	 */
	public static function getFooterLinks()
	{
		// get footerlinks
		$aFooterLinks = array();
		$aLinks = (isset(self::$aNavigation[FRONTEND_LANGUAGE][-2][-2])) ? self::$aNavigation[FRONTEND_LANGUAGE][-2][-2] : array();

		// loop rows
		foreach ($aLinks as $pageId => $row)
		{
			// redefine data
			$aTemp['title'] = $row['navigation'];
			$aTemp['url'] = $row['url'];

			// option
			$aTemp['oIsCurrentPage'] = (bool) ($pageId == FrontendPage::getCurrentPageId());

			// add to footerlinks
			$aFooterLinks[] = $aTemp;
		}

		// return
		return (array) $aFooterLinks;
	}


	/**
	 * Get the menu-keys
	 *
	 * @return	array
	 */
	public static function getKeys($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// does the keys exists in the cache?
		if(!isset(self::$aKeys[$language]) || empty(self::$aKeys[$language]))
		{
			// validate file
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/navigation/keys_'. $language .'.php')) throw new FrontendException('No key-file (keys_'. $language .'.php) found.');

			// require file
			require FRONTEND_CACHE_PATH .'/navigation/keys_'. $language .'.php';

			// store
			self::$aKeys[$language] = $keys;
		}

		// return
		return self::$aKeys[$language];
	}


	/**
	 * Get the navigation-items
	 *
	 * @return	array
	 * @param	string[optional] $language
	 */
	public static function getNavigation($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// does the keys exists in the cache?
		if(!isset(self::$aNavigation[$language]) || empty(self::$aNavigation[$language]))
		{
			// validate file
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/navigation/navigation_'. $language .'.php')) throw new FrontendException('No navigation-file (navigation_'. $language .'.php) found.');

			// require file
			require FRONTEND_CACHE_PATH .'/navigation/navigation_'. $language .'.php';

			// store
			self::$aNavigation[$language] = $navigation;
		}

		// return
		return self::$aNavigation[$language];
	}


	/**
	 * Get navigation html
	 *
	 * @return	string
	 * @param	int[optional] $startFromPageId
	 * @param	int[optional] $startDepth
	 * @param	int[optional] $endDepth
	 * @param	array[optional] $excludeIds
	 */
	public static function getNavigationHtml($startFromPageId = 0, $startDepth = 1, $endDepth = null, $excludeIds = array())
	{
		return (string) self::createHtml($startFromPageId, $startDepth, $endDepth, $excludeIds);
	}


	/**
	 * Get a menuid for an specified url
	 *
	 * @return	int
	 * @param 	string $url
	 * @param	string[optional] $language
	 */
	public static function getPageIdByUrl($url, $language = null)
	{
		// redefine
		$url = (string) $url;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// get menu items array
		$aKeys = self::getKeys($language);

		// get key
		$key = array_search($url, $aKeys);

		// return
		if($key === false) return 404;
		return (int) $key;
	}


	/**
	 * Get more info about a page
	 *
	 * @return	mixed
	 * @param	int $pageId
	 */
	public static function getPageInfo($pageId)
	{
		// get navigation
		$aNavigation = self::getNavigation();

		// loop levels
		foreach ($aNavigation as $depth => $aLevel)
		{
			// loop parents
			foreach ($aLevel as $parentId => $aChilds)
			{
				// loop childs
				foreach ($aChilds as $itemId => $aItem)
				{
					if($pageId == $itemId)
					{
						// set return
						$aReturn = $aItem;
						$aReturn['page_id'] = $itemId;

						// return
						return $aReturn;
					}
				}
			}
		}

		// fallback
		return false;
	}


	/**
	 * Get parentId
	 *
	 * @return	mixed
	 * @param	string $url
	 * @param	string[optional] $language
	 */
	public static function getParentIdByUrl($url, $language = null)
	{
		// redefine
		$url = (string) $url;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// init vars
		$aNavigation = self::getNavigation($language);
		$pageId = self::getPageIdByUrl($url, $language);

		// loop levels
		foreach ($aNavigation as $aLevel)
		{
			// loop parents
			foreach ($aLevel as $parentId => $aChilds)
			{
				// loop childs
				foreach ($aChilds as $itemId => $aItem)
				{
					if($pageId == $itemId) return (int) $parentId;
				}
			}
		}

		// fallback
		return false;
	}


	/**
	 * Get url for a given pageId
	 *
	 * @return	string
	 * @param	int $pageId
	 * @param	string[optional] $language
	 */
	public static function getUrlByPageId($pageId, $language = null)
	{
		// redefine
		$pageId = (int) $pageId;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// init url
		$url = (SITE_MULTILANGUAGE) ? '/'. $language .'/' : '/';

		// get the menuItems
		$aKeys = self::getKeys($language);

		// get the url, if it doens't exist return 404
		if(!isset($aKeys[$pageId])) return self::getUrlByPageId(404);

		// add url
		else $url .= $aKeys[$pageId];

		// return
		return $url;
	}


	/**
	 * Set the selected page ids
	 *
	 * @return	void
	 */
	public function setSelectedPageIds()
	{
		// get pages
		$aPages = (array) $this->url->getPages();

		// loop pages
		while(!empty($aPages))
		{
			// get page id
			$pageId = self::getPageIdByUrl((string) implode('/', $aPages));

			// add to selected item
			if($pageId !== false) self::$aSelectedPageIds[] = $pageId;

			// remove last element
			array_pop($aPages);
		}
	}
}

?>