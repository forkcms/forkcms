<?php
/**
 * Fork
 *
 * This source file is part of Fork CMS.
 *
 * @package		Frontend
 * @subpackage	Navigation
 *
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class FrontendNavigation
{
	/**
	 * The keys
	 *
	 * @var	array
	 */
	private static $aKeys = array(), $aNavigation = array();


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
	 * Get a menuid for an
	 *
	 * @return	int
	 * @param 	string $url
	 * @param	string[optional] $language
	 */
	public static function getMenuIdByUrl($url, $language = null)
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

		$aNavigation = self::getNavigation($language);
		$menuId = self::getMenuIdByUrl($url, $language);

		// loop levels
		foreach ($aNavigation as $depth => $aLevel)
		{
			// loop parents
			foreach ($aLevel as $parentId => $aChilds)
			{
				// loop childs
				foreach ($aChilds as $itemId => $aItem)
				{
					if($menuId == $itemId) return (int) $parentId;
				}
			}
		}

		// fallback
		return false;
	}


	/**
	 * Get url for a given menuId
	 *
	 * @return	string
	 * @param	int $menuId
	 * @param	string[optional] $language
	 */
	public static function getUrlByMenuId($menuId, $language = null)
	{
		// redefine
		$menuId = (int) $menuId;
		$language = ($language !== null) ? (string) $language : FRONTEND_LANGUAGE;

		$url = (SITE_MULTILANGUAGE) ? '/'. $language .'/' : '/';

		// get the menuItems
		$aKeys = self::getKeys($language);

		// get the url, if it doens't exist return 404
		if(!isset($aKeys[$menuId])) return self::getUrlByMenuId(404);

		// add url
		else $url .= $aKeys[$menuId];

		// return
		return $url;
	}
}
?>