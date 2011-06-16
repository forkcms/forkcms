<?php

/**
 * This class will be used to build the navigation
 *
 * @package		frontend
 * @subpackage	core
 *
 * @author		Tijs Verkoyen <tijs@netlash.com>
 * @author		Dieter Vanden Eynde <dieter@dieterve.be>
 * @since		2.0
 */
class FrontendNavigation extends FrontendBaseObject
{
	/**
	 * The keys an structural data for pages
	 *
	 * @var	array
	 */
	private static $keys = array(),
					$navigation = array();


	/**
	 * The selected pageIds
	 *
	 * @var	array
	 */
	private static $selectedPageIds = array();


	/**
	 * The path of the template to include, or that replaced the current one
	 *
	 * @var	string
	 */
	private static $templatePath;


	/**
	 * Default constructor.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		// call the parent
		parent::__construct();

		// set template path
		$this->setTemplatePath(FRONTEND_PATH . '/core/layout/templates/navigation.tpl');

		// set selected ids
		$this->setSelectedPageIds();
	}


	/**
	 * Creates a Backend URL for a given action and module
	 * If you don't specify a language the current language will be used.
	 *
	 * @return	string
	 * @param	string $action					The action to build the URL for.
	 * @param	string $module					The module to build the URL for.
	 * @param	string[optional] $language		The language to use, if not provided we will use the working language.
	 * @param	array[optional] $parameters		GET-parameters to use.
	 * @param	bool[optional] $urlencode		Should the parameters be urlencoded?
	 */
	public static function getBackendURLForBlock($action, $module, $language = null, array $parameters = null, $urlencode = true)
	{
		// redefine parameters
		$action = (string) $action;
		$module = (string) $module;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;
		$querystring = '';

		// add at least one parameter
		if(empty($parameters)) $parameters['token'] = 'true';

		// init counter
		$i = 1;

		// add parameters
		foreach($parameters as $key => $value)
		{
			// first element
			if($i == 1) $querystring .= '?' . $key . '=' . (($urlencode) ? urlencode($value) : $value);

			// other elements
			else $querystring .= '&amp;' . $key . '=' . (($urlencode) ? urlencode($value) : $value);

			// update counter
			$i++;
		}

		// build the URL and return it
		return '/private/' . $language . '/' . $module . '/' . $action . $querystring;
	}


	/**
	 * Get the first child for a given parent
	 *
	 * @return	mixed
	 * @param	int $pageId		The pageID wherefor we should retrieve the first child.
	 */
	public static function getFirstChildId($pageId)
	{
		// redefine
		$pageId = (int) $pageId;

		// init var
		$navigation = self::getNavigation();

		// loop depths
		foreach($navigation as $parent)
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
			// skip hidden pages
			if($data['hidden']) continue;

			// build temp array
			$temp = array();
			$temp['id'] = $id;
			$temp['url'] = self::getURL($id);
			$temp['title'] = $data['title'];
			$temp['navigation_title'] = $data['navigation_title'];
			$temp['selected'] = (bool) in_array($id, self::$selectedPageIds);

			// add rel
			if($data['no_follow']) $temp['rel'] = 'nofollow';

			// add
			$return[] = $temp;
		}

		// return footer links
		return $return;
	}


	/**
	 * Get the page-keys
	 *
	 * @return	array
	 * @param	string[optional] $language	The language wherefor the navigation should be loaded, if not provided we will load the language that was provided in the URL.
	 */
	public static function getKeys($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// does the keys exists in the cache?
		if(!isset(self::$keys[$language]) || empty(self::$keys[$language]))
		{
			// validate file
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH . '/navigation/keys_' . $language . '.php'))
			{
				// require BackendPagesModel
				require_once PATH_WWW . '/backend/core/engine/model.php';
				require_once PATH_WWW . '/backend/modules/pages/engine/model.php';

				// generate the cache
				BackendPagesModel::buildCache($language);

				// recall
				return self::getKeys($language);
			}

			// init var
			$keys = array();

			// require file
			require FRONTEND_CACHE_PATH . '/navigation/keys_' . $language . '.php';

			// validate keys
			if(empty($keys)) throw new FrontendException('No pages for ' . $language . '.');

			// store
			self::$keys[$language] = $keys;
		}

		// return from cache
		return self::$keys[$language];
	}


	/**
	 * Get the navigation-items
	 *
	 * @return	array
	 * @param	string[optional] $language	The language wherefor the keys should be loaded, if not provided we will load the language that was provided in the URL.
	 */
	public static function getNavigation($language = null)
	{
		// redefine
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// does the keys exists in the cache?
		if(!isset(self::$navigation[$language]) || empty(self::$navigation[$language]))
		{
			// validate file @later: the file should be regenerated
			if(!SpoonFile::exists(FRONTEND_CACHE_PATH . '/navigation/navigation_' . $language . '.php')) throw new FrontendException('No navigation-file (navigation_' . $language . '.php) found.');

			// init var
			$navigation = array();

			// require file
			require FRONTEND_CACHE_PATH . '/navigation/navigation_' . $language . '.php';

			// store
			self::$navigation[$language] = $navigation;
		}

		// return from cache
		return self::$navigation[$language];
	}


	/**
	 * Get navigation HTML
	 *
	 * @return	string
	 * @param	string[optional] $type			The type of navigation the HTML should be build for.
	 * @param	int[optional] $parentId			The parentID to start of.
	 * @param	int[optional] $depth			The maximum depth to parse.
	 * @param	array[optional] $excludeIds		PageIDs to be excluded.
	 * @param	bool[optional] $includeChildren	Children can be included regardless of whether we're at the current page.
	 * @param	int[optional] $depthCounter		A counter that will hold the current depth.
	 */
	public static function getNavigationHTML($type = 'page', $parentId = 0, $depth = null, $excludeIds = array(), $includeChildren = false, $depthCounter = 1)
	{
		// get navigation
		$navigation = self::getNavigation();

		// meta-navigation is requested but meta isn't enabled
		if($type == 'meta' && !FrontendModel::getModuleSetting('pages', 'meta_navigation', true)) return '';

		// validate
		if(!isset($navigation[$type])) throw new FrontendException('This type (' . $type . ') isn\'t a valid navigation type. Possible values are: page, footer, meta.');
		if(!isset($navigation[$type][$parentId])) throw new FrontendException('The parent (' . $parentId . ') doesn\'t exists.');

		// special construction to merge home with it's immediate children
		$mergedHome = false;
		while(true)
		{
			// loop elements
			foreach($navigation[$type][$parentId] as $id => $page)
			{
				// home is a special item, it should live on the same depth
				if($page['page_id'] == 1 && !$mergedHome)
				{
					// extra checks otherwise exceptions will wbe triggered.
					if(!isset($navigation[$type][$parentId]) || !is_array($navigation[$type][$parentId])) $navigation[$type][$parentId] = array();
					if(!isset($navigation[$type][$page['page_id']]) || !is_array($navigation[$type][$page['page_id']])) $navigation[$type][$page['page_id']] = array();

					// add children
					$navigation[$type][$parentId] = array_merge($navigation[$type][$parentId], $navigation[$type][$page['page_id']]);

					// mark as merged
					$mergedHome = true;

					// restart loop
					continue 2;
				}

				// not hidden
				if($page['hidden'])
				{
					unset($navigation[$type][$parentId][$id]);
					continue;
				}

				// some ids should be excluded
				if(in_array($page['page_id'], (array) $excludeIds))
				{
					unset($navigation[$type][$parentId][$id]);
					continue;
				}

				// if the item is in the selected page it should get an selected class
				if(in_array($page['page_id'], self::$selectedPageIds)) $navigation[$type][$parentId][$id]['selected'] = true;
				else $navigation[$type][$parentId][$id]['selected'] = false;

				// add nofollow attribute if needed
				if($page['no_follow']) $navigation[$type][$parentId][$id]['nofollow'] = true;
				else $navigation[$type][$parentId][$id]['nofollow'] = false;

				// has children and is selected and is desired?
				if(isset($navigation[$type][$page['page_id']]) && $page['page_id'] != 1 && ($navigation[$type][$parentId][$id]['selected'] == true || $includeChildren) && ($depth == null || $depthCounter + 1 <= $depth)) $navigation[$type][$parentId][$id]['children'] = self::getNavigationHTML($type, $page['page_id'], $depth, $excludeIds, $includeChildren, $depthCounter + 1);
				else $navigation[$type][$parentId][$id]['children'] = false;

				// add parent id
				$navigation[$type][$parentId][$id]['parent_id'] = $parentId;

				// add depth
				$navigation[$type][$parentId][$id]['depth'] = $depth;

				// set link
				$navigation[$type][$parentId][$id]['link'] = FrontendNavigation::getURL($page['page_id']);

				// is this an internal redirect?
				if(isset($page['redirect_page_id']) && $page['redirect_page_id'] != '') $navigation[$type][$parentId][$id]['link'] = FrontendNavigation::getURL((int) $page['redirect_page_id']);

				// is this an external redirect?
				if(isset($page['redirect_url']) && $page['redirect_url'] != '') $navigation[$type][$parentId][$id]['link'] = $page['redirect_url'];
			}

			// break the loop (it is only used for the special construction with home)
			break;
		}

		// create template
		$tpl = new FrontendTemplate(false);

		// assign navigation to template
		$tpl->assign('navigation', $navigation[$type][$parentId]);

		// return parsed content
		return $tpl->getContent(self::$templatePath, true, true);
	}


	/**
	 * Get a menuId for an specified URL
	 *
	 * @return	int
	 * @param 	string $URL						The URL wherfor you want a pageID.
	 * @param	string[optional] $language		The language wherefor the pageID should be retrieved, if not provided we will load the language that was provided in the URL.
	 */
	public static function getPageId($URL, $language = null)
	{
		// redefine
		$URL = trim((string) $URL, '/');
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
	 * @param	int $pageId		The pageID wherefor you want more information.
	 */
	public static function getPageInfo($pageId)
	{
		// get navigation
		$navigation = self::getNavigation();

		// loop levels
		foreach($navigation as $level)
		{
			// loop parents
			foreach($level as $children)
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
	 * @param	int $pageId						The pageID wherefor you want the URL.
	 * @param	string[optional] $language		The language wherein the URL should be retrieved, if not provided we will load the language that was provided in the URL.
	 */
	public static function getURL($pageId, $language = null)
	{
		// redefine
		$pageId = (int) $pageId;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// init URL
		$URL = (SITE_MULTILANGUAGE) ? '/' . $language . '/' : '/';

		// get the menuItems
		$keys = self::getKeys($language);

		// get the URL, if it doens't exist return 404
		if(!isset($keys[$pageId])) return self::getURL(404, $language);

		// add URL
		else $URL .= $keys[$pageId];

		// return the URL
		return $URL;
	}


	/**
	 * Get the URL for a give module & action combination
	 *
	 * @return	string
	 * @param	string $module					The module wherefor the URL should be build.
	 * @param	string[optional] $action		The specific action wherefor the URL shoul be build.
	 * @param	string[optional] $language		The language wherein the URL should be retrieved, if not provided we will load the language that was provided in the URL.
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
		foreach($navigation as $level)
		{
			// loop level
			foreach($level as $pages)
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
								// exact page was found, so return
								return self::getURL($properties['page_id'], $language);
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
			// build URL
			$URL = self::getURL($pageIdForURL, $language);

			// append action
			$URL .= '/' . FL::act(SpoonFilter::toCamelCase($action));

			// return the URL
			return $URL;
		}

		// fallback
		return self::getURL(404, $language);
	}


	/**
	 * Fetch the first direct link to an extra id
	 *
	 * @return	string
	 * @param	int $id							The id of the extra.
	 * @param	string[optional] $language		The language wherein the URL should be retrieved, if not provided we will load the language that was provided in the URL.
	 */
	public static function getURLForExtraId($id, $language = null)
	{
		// redefine
		$id = (int) $id;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		// get the menuItems
		$navigation = self::getNavigation($language);

		// loop types
		foreach($navigation as $level)
		{
			// loop level
			foreach($level as $pages)
			{
				// loop pages
				foreach($pages as $properties)
				{
					// only process pages with extra_blocks
					if(isset($properties['extra_blocks']))
					{
						// loop extras
						foreach($properties['extra_blocks'] as $extra)
						{
							// direct link?
							if($extra['id'] == $id)
							{
								// exact page was found, so return
								return self::getURL($properties['page_id'], $language);
							}
						}
					}
				}
			}
		}

		// fallback
		return self::getURL(404, $language);
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

		// no pages, means we're at the homepage
		if(empty($pages)) self::$selectedPageIds[] = 1;

		else
		{
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


	/**
	 * Set the path for the template
	 *
	 * @return	void
	 * @param	string $path	The path to set.
	 */
	private function setTemplatePath($path)
	{
		self::$templatePath = (string) $path;
	}
}

?>
