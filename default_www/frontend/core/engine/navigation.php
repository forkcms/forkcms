<?php

/**
 * FrontendNavigation
 * This class will be used to build the navigation
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendNavigation extends FrontendBaseObject
{
	/**
	 * The keys an structural data for pages
	 *
	 * @var	array
	 */
	private static	$keys = array(),
					$navigation = array();


	/**
	 * The selected pageIds
	 *
	 * @var	array
	 */
	private static $selectedPageIds = array();


	/**
	 * Class constructor.
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
	 * @param	int[optional] $parentId
	 * @param	int[optional] $depth
	 * @param	array[optional] $excludedIds
	 * @param	string[optional] $HTML
	 * @param	int[optional] $depthCounter
	 */
	private static function createHTML($type = 'page', $parentId = 0, $depth = null, $excludedIds = array(), $HTML = '', $depthCounter = 1)
	{
		// redefine
		$type = (string) $type;
		$parentId = (int) $parentId;
		$depth = ($depth !== null) ? (int) $depth : null;
		$excludedIds = (array) $excludedIds;
		$HTML = (string) $HTML;
		$depthCounter = (int) $depthCounter;

		// if the depthCounter exceeds the required depth return the generated HTML, we have the build the required HTML.
		if($depth !== null && $depthCounter > $depth) return $HTML;

		// init vars
		$defaultSelectedClass = 'selected';

		// fetch navigation
		$navigation = self::getNavigation();

		// validate
		if(!isset($navigation[$type])) throw new FrontendException('This type ('. $type .') isn\'t available in the navigation.');
		if(!isset($navigation[$type][$parentId])) throw new FrontendException('The parent ('. $parentId .') doesn\'t exists.');

		// start HTML, only when parentId is different from 1, the first level below home should be on the same level as home
		if($parentId != 1) $HTML .= '<ul>' . "\n";

		// loop elements
		foreach($navigation[$type][$parentId] as $page)
		{
			// some Ids should be excluded
			if(in_array($page['page_id'], $excludedIds)) continue;

			// if the item is in the selected page it should get an selected class
			if(in_array($page['page_id'], self::$selectedPageIds)) $HTML .= '	<li class="'. $defaultSelectedClass .'">'."\n";

			// just start the html
			else $HTML .= '	<li>'."\n";

			// add link
			$HTML .= '		<a href="'. FrontendNavigation::getURL($page['page_id']) .'">'. $page['navigation_title'] .'</a>'."\n";

			// has children?
			if(isset($navigation[$type][$page['page_id']]))
			{
				// home is a special item, it should live on the same depth
				if($page['page_id'] == 1) $depthCounter--;

				// add children
				$HTML = self::createHTML($type, $page['page_id'], $depth, $excludedIds, $HTML, ++$depthCounter);
			}

			// end HTML
			$HTML .= '	</li>'."\n";
		}

		// end HTML, only when parentId is different from 1, the first level below home should be on the same level.
		if($parentId != 1) $HTML .= '</ul>';

		// return
		return $HTML;
	}


	/**
	 * Get the first child for a given parent
	 *
	 * @return	mixed
	 * @param	int $pageId
	 */
	public static function getFirstChildId($pageId)
	{
		// redefine
		$pageId = (int) $pageId;

		// init var
		$navigation = self::getNavigation();

		// loop depths
		foreach($navigation as $depth => $parent)
		{
			// no availabe, skip this element
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
		// get the navigation
		$navigation = self::getNavigation();

		// init var
		$return = array();

		// validate
		if(!isset($navigation['footer'][0])) return $return;

		// loop links
		foreach($navigation['footer'][0] as $id => $data)
		{
			// build temp array
			$temp = array();
			$temp['id'] = $id;
			$temp['url'] = self::getURL($id);
			$temp['title'] = $data['title'];
			$temp['navigation_title'] = $data['navigation_title'];
			$temp['selected'] = (bool) in_array($id, self::$selectedPageIds);

			// add
			$return[] = $temp;
		}

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
			// validate file @later	the file should be regenerated
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
			// validate file @later: the file should be regenerated
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
	 * Get navigation HTML
	 *
	 * @return	string
	 * @param	string[optional] $type
	 * @param	int[optional] $parentId
	 * @param	int[optional] $depth
	 * @param	array[optional] $excludeIds
	 */
	public static function getNavigationHTML($type = 'page', $parentId = 0, $depth = null, $excludeIds = array())
	{
		return (string) self::createHTML($type, $parentId, $depth, $excludeIds);
	}


	/**
	 * Get a menuId for an specified url
	 *
	 * @return	int
	 * @param 	string $URL
	 * @param	string[optional] $language
	 */
	public static function getPageId($URL, $language = null)
	{
		// redefine
		$URL = (string) $URL;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// get menu items array
		$keys = self::getKeys($language);

		// get key
		$key = array_search($URL, $keys);

		// return 404 if we don't known a valid Id
		if($key === false) return 404;

		// return the real Id
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
				// loop children
				foreach($children as $itemId => $item)
				{
					// return if this is the requested item
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
	 * Get URL for a given pageId
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
		$URL = (SITE_MULTILANGUAGE) ? '/'. $language .'/' : '/';

		// get the menuItems
		$keys = self::getKeys($language);

		// get the url, if it doens't exist return 404
		if(!isset($keys[$pageId])) return self::getURL(404);

		// add url
		else $URL .= $keys[$pageId];

		// return
		return $URL;
	}


	/**
	 * Get the URL for a give module & action combination
	 *
	 * @return	string
	 * @param	string $module
	 * @param	string[optional] $action
	 * @param	string[optional] $language
	 */
	public static function getURLForBlock($module, $action = null, $language = null)
	{
		// redefine
		$module = (string) $module;
		$action = ($action !== null) ? (string) $action : null;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// init var
		$pageIdForURL = null;

		// get the menuItems
		$navigation = self::getNavigation($language);

		// loop types
		foreach($navigation as $type => $level)
		{
			// loop level
			foreach($level as $parentId => $pages)
			{
				// loop pages
				foreach($pages as $pageId => $properties)
				{
					// only process pages with extra_blocks
					if(isset($properties['extra_blocks']))
					{
						// loop extras
						foreach($properties['extra_blocks'] as $extra)
						{
							// direct link?
							if($extra['module'] == $module && $extra['action'] == $action)
							{
								// exacte page was found, so return
								return self::getURL($properties['page_id']);
							}

							// correct module but no action
							elseif($extra['module'] == $module && $extra['action'] == null)
							{
								// store pageId
								$pageIdForURL = (int) $pageId;
							}
						}
					}
				}
			}
		}

		// pageId stored?
		if($pageIdForURL !== null)
		{
			// build url
			$URL = self::getURL($pageIdForURL, $language);

			// append action
			$URL .= '/'. FrontendLanguage::getAction(SpoonFilter::toCamelCase($action));

			// return the URL
			return $URL;
		}

		// fallback
		return self::getURL(404);
	}

	/**
	 * Set the selected page ids
	 *
	 * @return	void
	 */
	public function setSelectedPageIds()
	{
		// get pages
		$pages = (array) $this->URL->getPages();

		// loop pages
		while(!empty($pages))
		{
			// get page id
			$pageId = self::getPageId((string) implode('/', $pages));

			// add pageId into selected items
			if($pageId !== false) self::$selectedPageIds[] = $pageId;

			// remove last element
			array_pop($pages);
		}
	}
}

?>