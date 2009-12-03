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
	private static	$keys = array(),
					$navigation = array();


	/**
	 * The page-ids for the selected pages
	 *
	 * @var	array
	 */
	private static $selectedPageIds = array();


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
	private static function createHtml($parentId = 0, $startDepth = 1, $endDepth = null, array $excludedIds = array(), $html = '')
	{
		// init vars
		$defaultSelectedClass = 'selected';

		// validation
		if($endDepth != null && $endDepth < $startDepth) return $html;

		// fetch navigation
		$navigation = self::getNavigation();

		// check if item exists
		if(isset($navigation[$startDepth][$parentId]))
		{
			// start html
			$html .= '<ul>' . "\n";

			// loop elements
			foreach ($navigation[$startDepth][$parentId] as $key => $value)
			{
				// check if elements should be excluded
				if(!in_array($key, (array) $excludedIds))
				{
					// check if this item should be selected
					$selected = (in_array($key, self::$selectedPageIds));
					$class = ($selected) ? $defaultSelectedClass : '';

					// add html
					if($class != null) $html .= "\t<li class=\"". $class ."\">" . "\n";
					else $html .= "\t<li>" . "\n";

					$html .= "\t\t". '<a href="'. self::getUrlByPageId($key) .'" title="'. $value['navigation'] .'">'. $value['navigation'] .'</a>' . "\n";

					// insert recursive here!
					if(isset($navigation[$startDepth + 1][$key]) && $selected) $html .= self::createHtml($key, $startDepth + 1, $endDepth, $excludedIds, '');

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


	// @todo phpdoc schrijven.
	public static function getFirstChildIdByPageId($pageId)
	{
		// redefine
		$pageId = (int) $pageId;

		// init var
		$navigation = self::getNavigation();

		// loop depths
		foreach($navigation as $depth => $parent)
		{
			// first check
			if(!isset($parent[$pageId])) continue;

			// get keys
			$keys = array_keys($parent[$pageId]);

			// get first item
			if(isset($keys[0])) return $keys[0];
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
		return;
		// get footerlinks
		$footerLinks = array();
		$links = (isset(self::$navigation[FRONTEND_LANGUAGE][-2][-2])) ? self::$navigation[FRONTEND_LANGUAGE][-2][-2] : array();

		// loop rows
		foreach($links as $pageId => $row)
		{
			// redefine data
			$tmp['title'] = $row['navigation'];
			$tmp['url'] = $row['url'];

			// option
			$tmp['oIsCurrentPage'] = (bool) ($pageId == FrontendPage::getCurrentPageId());

			// add to footerlinks
			$footerLinks[] = $tmp;
		}

		return $footerLinks;
	}


	/**
	 * Get the page-keys
	 *
	 * @return	array
	 */
	public static function getKeys($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// does the keys exists in the cache?
		if(!isset(self::$keys[$language]) || empty(self::$keys[$language]))
		{
			// validate file
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/pages/keys_'. $language .'.php')) throw new FrontendException('No key-file (keys_'. $language .'.php) found.');

			// require file
			require FRONTEND_CACHE_PATH .'/pages/keys_'. $language .'.php';

			// store
			self::$keys[$language] = $keys;
		}

		return self::$keys[$language];
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
		if(!isset(self::$navigation[$language]) || empty(self::$navigation[$language]))
		{
			// validate file
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/pages/navigation_'. $language .'.php')) throw new FrontendException('No navigation-file (navigation_'. $language .'.php) found.');

			// require file
			require FRONTEND_CACHE_PATH .'/pages/navigation_'. $language .'.php';

			// store
			self::$navigation[$language] = $navigation;
		}

		// return
		return self::$navigation[$language];
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
	public static function getPageIdByURL($url, $language = null)
	{
		// redefine
		$url = (string) $url;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// get menu items array
		$keys = self::getKeys($language);

		// get key
		$key = array_search($url, $keys);

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
		$navigation = self::getNavigation();

		// loop levels
		foreach($navigation as $depth => $level)
		{
			// loop parents
			foreach($level as $parentId => $children)
			{
				// loop childs
				foreach($children as $itemId => $item)
				{
					if($pageId == $itemId)
					{
						// set return
						$return = $item;
						$return['page_id'] = $itemId;

						// return
						return $return;;
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
	public static function getParentIdByURL($url, $language = null)
	{
		// redefine
		$url = (string) $url;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// init vars
		$navigation = self::getNavigation($language);
		$pageId = self::getPageIdByURL($url, $language);

		// loop levels
		foreach($navigation as $level)
		{
			// loop parents
			foreach($level as $parentId => $children)
			{
				// loop childs
				foreach($children as $itemId => $item)
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
	public static function getURLByPageId($pageId, $language = null)
	{
		// redefine
		$pageId = (int) $pageId;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// init url
		$url = (SITE_MULTILANGUAGE) ? '/'. $language .'/' : '/';

		// get the menuItems
		$keys = self::getKeys($language);

		// get the url, if it doens't exist return 404
		if(!isset($keys[$pageId])) return self::getURLByPageId(404);

		// add url
		else $url .= $keys[$pageId];

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
		$pages = (array) $this->url->getPages();

		// loop pages
		while(!empty($pages))
		{
			// get page id
			$pageId = self::getPageIdByURL((string) implode('/', $pages));

			// add to selected item
			if($pageId !== false) self::$selectedPageIds[] = $pageId;

			// remove last element
			array_pop($pages);
		}
	}
}

?>