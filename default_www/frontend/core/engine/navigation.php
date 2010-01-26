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
	 * Creates the HTML for the menu
	 *
	 * @return	string
	 * @param	string[optional] $type
	 * @param	int[optional] $startDepth
	 * @param	int[optional] $maxDepth
	 * @param	array[optional] $excludedIds
	 * @param	string[optional] $html
	 */
	private static function createHtml($type = 'page', $parentId = 0, $depth = null, $excludedIds = array(), $html = '', $depthCounter = 1)
	{
		// redefine
		$type = (string) $type;
		$excludedIds = (array) $excludedIds;
		$html = (string) $html;

		// if the depthCounter exceeds the required depth return the generated HTML, we have the build the required HTML.
		if($depth !== null && $depthCounter > $depth) return $html;

		// init vars
		$defaultSelectedClass = 'selected';

		// fetch navigation
		$navigation = self::getNavigation();

		// validate
		if(!isset($navigation[$type])) throw new FrontendException('This type ('. $type .') isn\'t available in the navigation.');
		if(!isset($navigation[$type][$parentId])) throw new FrontendException('The parent ('. $parentId .') doesn\'t exists.');

		// start HTML, only when parentId is different from 1, the first level below home should be on the same level.
		if($parentId != 1) $html .= '<ul>' . "\n";

		// loop elements
		foreach($navigation[$type][$parentId] as $page)
		{
			// some IDs should be excluded
			if(in_array($page['page_id'], $excludedIds)) continue;

			// start HTML
			if(in_array($page['page_id'], self::$selectedPageIds)) $html .= '	<li class="'. $defaultSelectedClass .'">'."\n";
			else $html .= '	<li>'."\n";

			// add link
			$html .= '		<a href="'. FrontendNavigation::getURL($page['page_id']) .'">'. $page['navigation_title'] .'</a>'."\n";

			// has children?
			if(isset($navigation[$type][$page['page_id']]))
			{
				// home is a special item, it should live on the same depth
				if($page['page_id'] == 1) $depthCounter--;

				// add children
				return self::createHtml($type, $page['page_id'], $depth, $excludedIds, $html, ++$depthCounter);
			}

			// end HTML
			$html .= '	</li>'."\n";
		}

		// end HTML, only when parentId is different from 1, the first level below home should be on the same level.
		if($parentId != 1) $html .= '</ul>';

		// return
		return $html;
	}


//	// @todo phpdoc schrijven.
//	public static function getFirstChildIdByPageId($pageId)
//	{
//		// redefine
//		$pageId = (int) $pageId;
//
//		// init var
//		$navigation = self::getNavigation();
//
//		// loop depths
//		foreach($navigation as $depth => $parent)
//		{
//			// first check
//			if(!isset($parent[$pageId])) continue;
//
//			// get keys
//			$keys = array_keys($parent[$pageId]);
//
//			// get first item
//			if(isset($keys[0])) return $keys[0];
//		}
//
//		// fallback
//		return false;
//	}

	/**
	 * Get all footerlinks
	 *
	 * @return	array
	 */
	public static function getFooterLinks()
	{
		// get the navigation
		$navigation = self::getNavigation();

		// init var
		$return = array();

		// validate
		if(!isset($navigation['footer'][0])) return $return;

		// loop links
		foreach($navigation['footer'][0] as $id => $data)
		{
			// temp array
			$temp = array();
			$temp['id'] = $id;
			$temp['url'] = self::getURL($id);
			$temp['title'] = $data['title'];
			$temp['navigation_title'] = $data['navigation_title'];
			$temp['selected'] = (bool) in_array($id, self::$selectedPageIds);

			// add
			$return[] = $temp;
		}

		// return the links
		return $return;
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
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/navigation/keys_'. $language .'.php')) throw new FrontendException('No key-file (keys_'. $language .'.php) found.');

			// init var
			$keys = array();

			// require file
			require FRONTEND_CACHE_PATH .'/navigation/keys_'. $language .'.php';

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
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH .'/navigation/navigation_'. $language .'.php')) throw new FrontendException('No navigation-file (navigation_'. $language .'.php) found.');

			// init var
			$navigation = array();

			// require file
			require FRONTEND_CACHE_PATH .'/navigation/navigation_'. $language .'.php';

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
	 * @param	string[optional] $type
	 * @param	int[optional] $startDepth
	 * @param	int[optional] $endDepth
	 * @param	array[optional] $excludeIds
	 */
	public static function getNavigationHtml($type = 'page', $parentId = 0, $depth = null, $excludeIds = array())
	{
		return (string) self::createHtml($type, $parentId, $depth, $excludeIds);
	}


	/**
	 * Get a menuid for an specified url
	 *
	 * @return	int
	 * @param 	string $url
	 * @param	string[optional] $language
	 */
	public static function getPageId($url, $language = null)
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
		foreach($navigation as $type => $level)
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
//
//
//	/**
//	 * Get parentId
//	 *
//	 * @return	mixed
//	 * @param	string $url
//	 * @param	string[optional] $language
//	 */
//	public static function getParentIdByURL($url, $language = null)
//	{
//		// redefine
//		$url = (string) $url;
//		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;
//
//		// init vars
//		$navigation = self::getNavigation($language);
//		$pageId = self::getPageId($url, $language);
//
//		// loop levels
//		foreach($navigation as $level)
//		{
//			// loop parents
//			foreach($level as $parentId => $children)
//			{
//				// loop childs
//				foreach($children as $itemId => $item)
//				{
//					if($pageId == $itemId) return (int) $parentId;
//				}
//			}
//		}
//
//		// fallback
//		return false;
//	}
//
//
	/**
	 * Get url for a given pageId
	 *
	 * @return	string
	 * @param	int $pageId
	 * @param	string[optional] $language
	 */
	public static function getURL($pageId, $language = null)
	{
		// redefine
		$pageId = (int) $pageId;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// init url
		$url = (SITE_MULTILANGUAGE) ? '/'. $language .'/' : '/';

		// get the menuItems
		$keys = self::getKeys($language);

		// get the url, if it doens't exist return 404
		if(!isset($keys[$pageId])) return self::getURL(404);

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
			$pageId = self::getPageId((string) implode('/', $pages));

			// add to selected item
			if($pageId !== false) self::$selectedPageIds[] = $pageId;

			// remove last element
			array_pop($pages);
		}
	}
}

?>